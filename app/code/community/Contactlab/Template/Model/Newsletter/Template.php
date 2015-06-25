<?php

/** Newsletter template model rewrite. */
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
     * @return Contactlab_Commons_Model_Task
     */
    private function _createTask(Mage_Newsletter_Model_Queue $queue, $storeId, $max) {
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
        return $this->getTemplateQueueProcessor($storeId)->getPriceFor($product, $item);
    }

    /**
     * Send to all customers?
     * @param string $code
     * @param string $storeId
     */
    public function doSendToAllCustomers($code, $storeId) {
        if ($code !== 'CART' && $code !== 'WISHLIST') {
            $code = 'GENERIG';
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
     * @param type $field
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
}
