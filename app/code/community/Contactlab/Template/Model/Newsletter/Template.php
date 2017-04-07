<?php

/**
 * Newsletter template model rewrite.
 * @method getAndOr()
 * @method getMaxProducts()
 * @method getMinProducts()
 * @method getMaxValue()
 * @method getMinValue()
 * @method getMaxMinutesFromLastUpdate()
 * @method getMinMinutesFromLastUpdate()
 * @method getPriority()
 * @method getQueueDelayTime()
 * @method getCronDateRangeEnd()
 * @method getCronDateRangeStart()
 * @method getIsCronEnabled()
 * @method getProductImageSize()
 * @method getDefaultProductSnippet()
 * @method getIsTestMode()
 * @method getFlgHtmlTxt()
 * @method getTemplateTextPlain()
 * @method getTemplateTypeId()
 * @method getReplyTo()
 * @method getEnableXmlDelivery()
 * @method getDontRunNow()
 * @method getStoreId()
 * @method getDebugAddress()
 * @method getDebugInfo()
 * @method getTemplateId()
 *
 * @method Contactlab_Template_Model_Newsletter_Template setAndOr($value)
 * @method Contactlab_Template_Model_Newsletter_Template setMaxProducts($value)
 * @method Contactlab_Template_Model_Newsletter_Template setMinProducts($value)
 * @method Contactlab_Template_Model_Newsletter_Template setMaxValue($value)
 * @method Contactlab_Template_Model_Newsletter_Template setMinValue($value)
 * @method Contactlab_Template_Model_Newsletter_Template setMaxMinutesFromLastUpdate($value)
 * @method Contactlab_Template_Model_Newsletter_Template setMinMinutesFromLastUpdate($value)
 * @method Contactlab_Template_Model_Newsletter_Template setPriority($value)
 * @method Contactlab_Template_Model_Newsletter_Template setQueueDelayTime($value)
 * @method Contactlab_Template_Model_Newsletter_Template setCronDateRangeEnd($value)
 * @method Contactlab_Template_Model_Newsletter_Template setCronDateRangeStart($value)
 * @method Contactlab_Template_Model_Newsletter_Template setIsCronEnabled($value)
 * @method Contactlab_Template_Model_Newsletter_Template setProductImageSize($value)
 * @method Contactlab_Template_Model_Newsletter_Template setDefaultProductSnippet($value)
 * @method Contactlab_Template_Model_Newsletter_Template setIsTestMode($value)
 * @method Contactlab_Template_Model_Newsletter_Template setFlgHtmlTxt($value)
 * @method Contactlab_Template_Model_Newsletter_Template setTemplateTextPlain($value)
 * @method Contactlab_Template_Model_Newsletter_Template setTemplateTypeId($value)
 * @method Contactlab_Template_Model_Newsletter_Template setReplyTo($value)
 * @method Contactlab_Template_Model_Newsletter_Template setEnableXmlDelivery($value)
 * @method Contactlab_Template_Model_Newsletter_Template setDebugInfo($value)
 * @method Contactlab_Template_Model_Newsletter_Template setTemplateTypeModel($value)
 * @method Contactlab_Template_Model_Newsletter_Template setDebugAddress($value)
 *
 * @method bool hasTemplateTypeId()
 * @method bool hasTemplateTypeModel()
 * @method bool hasQueueDelayTime()
 * @method bool hasDebugAddress()
 * @method bool hasDebugInfo()
 * @method bool hasMinProducts()
 * @method bool hasMaxProducts()
 */
class Contactlab_Template_Model_Newsletter_Template extends Mage_Newsletter_Model_Template {
    /**
     * Process template queue.
     *
     * @param string $storeId
     * @return string
     */
    public function processTemplateQueue($storeId) {
        $resource = Mage::getSingleton('core/resource');
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

        // $helper->logDebug("Process " . $this->getTemplateCode() . " template, store " . $storeId);
        /* @var $processor Contactlab_Template_Model_Newsletter_Processor_Abstract  */
        $processor = $this->getTemplateQueueProcessor($storeId)->setStoreId($storeId);
        if (!$processor->isEnabled()) {
            return null;
        }

        $c = 0;
        $queue = null;
        foreach ($processor->loadSubscribers($this, false) as $item) {
            /** @var $item Contactlab_Subscribers_Model_Newsletter_Subscriber */
            if (is_null($queue)) {
                $queue = Mage::getModel('newsletter/queue');
                $queue->setTemplateId($this->getId());
                $queue->setNewsletterType($this->getTemplateType());
                $queue->setNewsletterSubject($this->getTemplateSubject());
                $queue->setNewsletterSenderName($this->getTemplateSenderName());
                $queue->setNewsletterSenderEmail($this->getTemplateSenderEmail());
                $queue->save();
            }
            $data['queue_id'] = $queue->getId();
            $data['queued_at'] = Mage::getSingleton('core/date')->gmtDate();
            $data['subscriber_id'] = $item->getSubscriberId();
            $data['customer_id'] = $item->getCustomerId();
            $data['product_ids'] = $item->getProductIds();
            $adapter->insert($resource->getTableName('newsletter/queue_link'), $data);

            $c++;
        }
        if ($processor->getSendToAllCustomers()) {
            foreach ($processor->loadSubscribers($this, true) as $item) {
                if (is_null($queue)) {
                    $queue = Mage::getModel('newsletter/queue');
                    $queue->setTemplateId($this->getId());
                    $queue->setNewsletterType($this->getTemplateType());
                    $queue->setNewsletterSubject($this->getTemplateSubject());
                    $queue->setNewsletterSenderName($this->getTemplateSenderName());
                    $queue->setNewsletterSenderEmail($this->getTemplateSenderEmail());
                    $queue->save();
                }
                $data['queue_id'] = $queue->getId();
                $data['queued_at'] = Mage::getSingleton('core/date')->gmtDate();
                $data['subscriber_id'] = $item->getSubscriberId();
                $data['customer_id'] = $item->getEntityId();
                $data['product_ids'] = $item->getProductIds();
                $adapter->insert($resource->getTableName('newsletter/queue_link'), $data);

                $c++;
            }
        }
        $this->setDebugInfo($processor->getDebugInfo());
        if ($c > 0) {
            $this->_createTask($queue, $storeId, $c);
            return "Found " . $c . " subscribers " . $queue->getQueueId();
        } else {
            return null;
        }
    }


    /**
     * Get Template queue processor model.
     * @param string $storeId
     * @return Contactlab_Template_Model_Newsletter_Processor_Abstract
     */
    public function getTemplateQueueProcessor($storeId) {
        $code = $this->getTemplateTypeModel()->getTemplateTypeCode();
        /* @var $rv Contactlab_Template_Model_Newsletter_Processor_Abstract */
        $rv = Mage::getModel('contactlab_template/newsletter_processor_'
            . strtolower($code));
        if (!$rv) {
            $rv = Mage::getModel('contactlab_template/newsletter_processor_generic');
            // throw new Zend_Exception("No processor found for type $code");
        }
        $rv->setSendToAllCustomers($this->doSendToAllCustomers($code, $storeId));
        return $rv;
    }

    /**
     * Get Template type model.
     *
     * @return Contactlab_Template_Model_Type
     */
    public function getTemplateTypeModel() {
        if (!$this->hasTemplateTypeId()) {
            return $this;
        }
        if (!$this->hasTemplateTypeModel()) {
            $this->setTemplateTypeModel(
                Mage::getModel('contactlab_template/type')
                    ->load($this->getTemplateTypeId()));
        }
        /** @noinspection PhpUndefinedMethodInspection */
        return parent::getTemplateTypeModel();
    }


    /**
     * Is xml delivery enabled?
     *
     * @return boolean
     */
    public function isXmlDeliveryEnabled() {
        return $this->getEnableXmlDelivery() != 0;
    }

    /**
     * Create task from queue.
     *
     * @param Mage_Newsletter_Model_Queue $queue
     * @param string $storeId
     * @param int $max
     * @return Contactlab_Commons_Model_Task
     * @throws Exception
     */
    private function _createTask(Mage_Newsletter_Model_Queue $queue, $storeId, $max) {
        /** @var $queue Contactlab_Template_Model_Newsletter_Queue */
        /** @var $rv Contactlab_Commons_Model_Task */
        $rv = Mage::getModel("contactlab_commons/task")
                ->setDescription(sprintf('Process "%s" queue [%d]',
                    $this->getTemplateSubject(), $queue->getId()))
                ->setTaskCode("ProcessNewsletterQueueTask")
                ->setModelName('contactlab_template/task_processNewsletterQueueRunner')
                ->setTaskData(serialize(array(
                    'queue_id' => $queue->getQueueId(),
                    'store_id' => $storeId
                )))->setMaxValue($max);

        // Queue delay time
        if ($this->hasQueueDelayTime() && $this->getQueueDelayTime() > 0) {
            $date = Mage::getModel('core/date');
            $rv->setPlannedAt($date->gmtTimestamp()
                    + ($this->getQueueDelayTime() * 60))->save();
            $runNow = false;
        } else {
            $runNow = true;
        }
        if ($this->getDontRunNow()) {
            $runNow = false;
        }
        $rv->save();
        $queue->setTaskId($rv->getTaskId())->save();
        if ($runNow) {
            $rv->runTask();
        }
        return $rv;
    }

    /**
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param string[] $item
     * @return string
     */
    public function getPriceFor(Mage_Catalog_Model_Product $product, array $item, $storeId) {           
    	return $this->getTemplateQueueProcessor($storeId)->getPriceFor($product->setStoreId($storeId), $item);
    }

    /**
     * Send to all customers?
     * @param string $code
     * @param string $storeId
     * @return bool
     */
    public function doSendToAllCustomers($code, $storeId) {
        if ($code !== 'CART' && $code !== 'WISHLIST') {
            $code = 'GENERIC';
        }
        $code = strtolower($code);
        return Mage::getStoreConfigFlag("contactlab_template/$code/send_to_not_subscribed", $storeId);
    }

    /**
     * Validation of min/max values.
     */
    public function validate() {
        parent::validate();
        /* @var $helper Contactlab_Template_Helper_Data */
        $helper = Mage::helper('contactlab_template');
        $errors = array();
        $this->validateMinMaxFields(
                array(
                    'minutes_from_last_update' => array(
                        'min' => 'Minimum number of minutes',
                        'max' => 'Maximum number of minutes'
                    ),
                    'value' => array(
                        'min' => 'Minimum value',
                        'max' => 'Maximum value'
                    ),
                    'products' => array(
                        'min' => 'Minimum number of products',
                        'max' => 'Maximum number of products'
                    ),
                ), $helper, $errors);
        if (!empty($errors)) {
            Mage::throwException(join("\n", $errors));
        }
    }

    /**
     * Validate min and max values
     * @param array $fields
     * @param Contactlab_Template_Helper_Data $helper
     * @param array $errors
     */
    public function validateMinMaxFields(array $fields, Contactlab_Template_Helper_Data $helper, array &$errors) {
        foreach ($fields as $field => $labels) {
            $this->validateMinMax($field, $labels, $helper, $errors);
        }
    }

    /**
     * Validate min and max value
     * @param String $field
     * @param array $labels
     * @param Contactlab_Template_Helper_Data $helper
     * @param array $errors
     */
    public function validateMinMax($field, array $labels, Contactlab_Template_Helper_Data $helper, array &$errors) {
        $min = trim($this->getData('min_' . $field));
        $max = trim($this->getData('max_' . $field));
        if ($min === '' || $max === '') {
            return;
        }
        if ($min > $max) {
            $errors[] = $helper->__("\"%s\" cannot be greater than \"%s\"",
                    $helper->__($labels['min']),
                    $helper->__($labels['max']));
        }
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Newsletter_Model_Template
     */
    protected function _beforeSave()
    {
        // manage "none" store value.
        if ($this->getStoreId() == 'none') {
            $this->setData('store_id', null);
        }
        return parent::_beforeSave();
    }
}
