<?php

/**
 * Abstract processor.
 * This abstract class and each implementing class is designed for
 * search and filter subscribers for each template to be queued.
 */
abstract class Contactlab_Template_Model_Newsletter_Processor_Abstract
        extends Varien_Object
        implements Contactlab_Template_Model_Newsletter_Processor_Interface {

    public function _construct() {
        parent::_construct();
        $this->setFilters(array());
    }

    /* @var Mage_Core_Model_Resource_Db_Collection_Abstract $_collection The collection. */
    private $_collection;

    /**
     * Load subscribers.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @param boolean $onlyCustomers
     * @return a collection
     */
    public final function loadSubscribers(Mage_Newsletter_Model_Template $template, $onlyCustomers) {
        /* @var $rv Mage_Core_Model_Resource_Db_Collection_Abstract */
        if ($onlyCustomers) {
            $rv = $this->loadCustomers($template);
        } else {
            $rv = $this->loadNewsletterSubscribers($template, $onlyCustomers);
        }
        /* @var $h Contactlab_Commons_Helper_Data */
        return $rv;
    }

    protected final function loadNewsletterSubscribers(Mage_Newsletter_Model_Template $template, $onlyCustomers) {
        $this->_collection = Mage::getResourceModel('newsletter/subscriber_collection');
        if (!$onlyCustomers) {
            $this->_collection->useOnlySubscribed();
        }
        $this->setIsTestMode($template->getIsTestMode());
        if ($this->getIsTestMode()) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_testMode');
        }
        $this->setTemplate($template);

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
     * @param Mage_Newsletter_Model_Template $template
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected final function loadCustomers(Mage_Newsletter_Model_Template $template) {
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
     * @param Contactlab_Template_Model_Newsletter_Processor_Filter_Interface instance $filter
     * @param $parameters = array()
     * @return $this
     */
    public function applyFilter($filter, $parameters = array()) {
        if ($this->hasStop() && $this->getStop()) {
            return $this;
        }
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
     * @param Mage_Newsletter_Model_Template $template
     * @return void
     */
    protected abstract function applySubscribersFilter(Mage_Newsletter_Model_Template $template);

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
        return parent::setStoreId($storeId);
    }

    /**
     * Get store id.
     *
     * @return string
     */
    public function getStoreId() {
        return parent::getStoreId();
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
