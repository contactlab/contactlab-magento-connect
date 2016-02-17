<?php

/**
 * Abstract processor.
 * This abstract class and each implementing class is designed for
 * search and filter subscribers for each template to be queued.
 *
 * @method getSendToAllCustomers()
 * @method getDebugInfo()
 * @method array getFilters()
 * @method bool getIsTestMode()
 * @method Mage_Newsletter_Model_Template getTemplate()
 * @method bool getStop()
 *
 * @method bool hasDebugInfo()
 * @method bool hasTemplate()
 * @method bool hasStop()
 *
 * @method unsStop()
 *
 * @method Contactlab_Template_Model_Newsletter_Processor_Abstract setSendToAllCustomers($value)
 * @method Contactlab_Template_Model_Newsletter_Processor_Abstract setDebugInfo($value)
 * @method Contactlab_Template_Model_Newsletter_Processor_Abstract setFilters($value)
 * @method Contactlab_Template_Model_Newsletter_Processor_Abstract setIsTestMode($value)
 * @method Contactlab_Template_Model_Newsletter_Processor_Abstract setTemplate(Mage_Newsletter_Model_Template $template)
 * @method Contactlab_Template_Model_Newsletter_Processor_Abstract setStop($value)
 */
abstract class Contactlab_Template_Model_Newsletter_Processor_Abstract
        extends Varien_Object
        implements Contactlab_Template_Model_Newsletter_Processor_Interface {

    public function _construct() {
        parent::_construct();
        $this->setFilters(array());
    }

    /* @var Contactlab_Template_Model_Resource_Newsletter_Subscriber_Collection $_collection The collection. */
    private $_collection;

    /**
     * Load subscribers.
     *
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @param boolean $onlyCustomers
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public final function loadSubscribers(Contactlab_Template_Model_Newsletter_Template $template, $onlyCustomers) {
        /* @var $rv Mage_Core_Model_Resource_Db_Collection_Abstract */
        if ($onlyCustomers) {
            $rv = $this->loadCustomers($template);
        } else {
            $rv = $this->loadNewsletterSubscribers($template, $onlyCustomers);
        }
        /* @var $h Contactlab_Commons_Helper_Data */
        return $rv;
    }

    /**
     * Load newsletter subscribers.
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @param $onlyCustomers
     * @return Contactlab_Template_Model_Resource_Newsletter_Subscriber_Collection|Mage_Newsletter_Model_Resource_Subscriber_Collection
     * @throws Zend_Exception
     */
    protected final function loadNewsletterSubscribers(Contactlab_Template_Model_Newsletter_Template $template, $onlyCustomers) {
        $this->_collection = Mage::getResourceModel('newsletter/subscriber_collection');
        if (!$onlyCustomers) {
            $this->_collection->useOnlySubscribed();
        }
        $this->setTemplate($template);
        $this->setIsTestMode($template->getIsTestMode());
        if ($this->getIsTestMode()) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_testMode');
        }

        if ($template->hasDebugAddress() && $template->getDebugAddress() != '') {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_emailLike',
                    array('eq' => $template->getDebugAddress()));
        }

        $this->applySubscribersFilter($template);
        $this->unsStop();
        foreach ($this->getFilters() as $filter) {
            $this->applyFilter($filter['name'], $filter['params']);
        }
        if ($this->_collection->count() > 0) {
            Mage::helper('contactlab_commons')
                ->logInfo(sprintf("%d row(s) subscriber found for cron template %s and store %s",
                    $this->_collection->count(),
                    $template->getTemplateSubject(),
                    $this->getStoreId()));
        }
        // Mage::helper('contactlab_commons')->logInfo($this->_collection->getSelect()->assemble());
        return $this->_collection;
    }

    /**
     * Load customers.
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected final function loadCustomers(Contactlab_Template_Model_Newsletter_Template $template) {
        /* @var $subscribers Mage_Core_Model_Resource_Db_Abstract */
        $subscribers = Mage::getResourceModel("newsletter/subscriber");
        $subscribersTable = $subscribers->getMainTable();

        $this->_collection = Mage::getResourceModel('customer/customer_collection');
        $this->_collection->getSelect()->where('e.entity_id not in (select customer_id from ' . $subscribersTable . ')');
        $this->setIsTestMode($template->getIsTestMode());
        if ($this->getIsTestMode()) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_testMode');
        }

        if ($template->hasDebugAddress() && $template->getDebugAddress() != '') {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_emailLike',
                    array('eq' => $template->getDebugAddress()));
        }

        $this->applySubscribersFilter($template);
        $this->unsStop();
        foreach ($this->getFilters() as $filter) {
            $this->applyFilter($filter['name'], $filter['params']);
        }
        if ($this->_collection->count() > 0) {
            Mage::helper('contactlab_commons')
                ->logInfo(sprintf("%d customer row(s) found for cron template %s and store %s",
                    $this->_collection->count(),
                    $template->getTemplateSubject(),
                    $this->getStoreId()));
        }
        // Mage::helper('contactlab_commons')->logInfo($this->_collection->getSelect()->assemble());
        return $this->_collection;
    }

    /**
     * Apply filter $filter withi $params array to the cached collection.
     *
     * @param $filter
     * @param array $parameters = array()
     * @return $this
     * @throws Zend_Exception
     */
    public function applyFilter($filter, $parameters = array()) {
        if ($this->hasStop() && $this->getStop()) {
            return $this;
        }
        /**
         * @var $filterModel Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract
         */
        $filterModel = Mage::getModel($filter);
        if (!is_object($filterModel)) {
            throw new Zend_Exception("Could not find $filter model");
        }
        $filterModel->setStoreId($this->getStoreId());
        $filterModel->setSendToAllCustomers($this->getSendToAllCustomers());
        // Disable filters in test mode
        if ($this->getIsTestMode() && !$filterModel->doRunInTestMode()) {
            return $this;
        }
        $countBefore = 0;
        if ($this->doOutputRows()) {
            $countBefore = $this->_collection->getRealSize();
        }
        $filterModel->applyFilter($this->_collection, $parameters);
        if ($this->doOutputRows()) {
            $countAfter = $this->_collection->getRealSize();
            if ($countAfter == 0) {
                $this->setStop(true);
            }
            if ($countAfter != $countBefore) {
                $this->addDebugInfo(sprintf("[Store %s %s] - %s filter applied. %d rows before, %d rows after" . ($countAfter == 0 ? ' <em>Exit loop</em>' : ''),
                        $this->getStoreId(),
                        $this->getTemplate()->getTemplateSubject(),
                        $filterModel->getName(), $countBefore, $countAfter));
            }
        }
        return $this;
    }

    /**
     * Helper function getConfig.
     *
     * @param string $group
     * @param string $field
     * @deprecated since version 0.8.9
     * @return string
     */
    public function getConfig($group, $field) {
        return Mage::getStoreConfig("contactlab_template/$group/$field", $this->getStoreId());
    }

    /**
     * applySubscribersFilter: Load collection (abstract).
     *
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @return void
     */
    protected abstract function applySubscribersFilter(Contactlab_Template_Model_Newsletter_Template $template);

    /**
     * Get processor code.
     *
     * @return string
     */
    public abstract function getProcessorCode();

    /**
     * Set store id.
     *
     * @param String $storeId
     * @return $this
     */
    public function setStoreId($storeId) {
        return parent::setData('store_id', $storeId);
    }

    /**
     * Get store id.
     *
     * @return string
     */
    public function getStoreId() {
        return parent::getData('store_id');
    }

    /**
     * Is enabled.
     *
     * @return bool
     */
    public function isEnabled() {
        return Mage::getStoreConfigFlag('contactlab_template/'
            . $this->getProcessorCode() . '/enabled', $this->getStoreId());
    }

    /**
     * Get price for product.
     * @param Mage_Catalog_Model_Product $product
     * @param string[] $item
     * @return string
     */
    public abstract function getPriceFor(Mage_Catalog_Model_Product $product, array $item);

    /**
     * Add a filter.
     * @param string $filterName
     * @param array $params
     */
    public function addFilter($filterName, array $params = array()) {
        $filters = $this->getFilters();
        $filters[] = array('name' => $filterName, 'params' => $params);
        $this->setFilters($filters);
    }

    /**
     * Set debug info.
     * @param string $message
     */
    public function addDebugInfo($message) {
        if (!$this->hasDebugInfo()) {
            $this->setDebugInfo(array());
        }
        $info = $this->getDebugInfo();
        /* @var $info Mage_Core_Model_Message */
        $messageEntity = Mage::getSingleton('core/message');
        $info[] = $messageEntity->notice($message);
        $this->setDebugInfo($info);
    }

    /**
     * Should output rows to session messages?
     * @return boolean.
     */
    public function doOutputRows() {
        /* @var $helper Contactlab_Template_Helper_Data */
        $helper = Mage::helper("contactlab_template");
        /* @var $helperC Contactlab_Commons_Helper_Data */
        $helperC = Mage::helper("contactlab_commons");

        return $helper->shouldSendMessages() && $helperC->isDebug();
    }
}
