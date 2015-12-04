<?php

/**
 * Newsletter queue model rewrite.
 *
 * @method getQueueId()
 * @method getTaskId()
 *
 * @method Contactlab_Template_Model_Newsletter_Queue setQueueId($value)
 * @method Contactlab_Template_Model_Newsletter_Queue setTaskId($value)
 * @method Contactlab_Template_Model_Newsletter_Queue setXmlDelivery(Contactlab_Template_Model_Newsletter_XmlDelivery $value)
 *
 * @method hasQueueId()
 */
class Contactlab_Template_Model_Newsletter_Queue extends Mage_Newsletter_Model_Queue {
    /**
     * Subscribers collection
     * @var Varien_Data_Collection_Db
     */
    protected $_subscribersCustomerCollection = null;

    /**
     * Send messages to subscribers for this queue
     *
     * @param   int     $count
     * @param   array   $additionalVariables
     * @return Mage_Newsletter_Model_Queue
     */
    public function sendPerSubscriber($count=20, array $additionalVariables=array()) {
        if ($this->getTemplate()->isXmlDeliveryEnabled()) {
            return $this->sendXmlDelivery();
        } else {
            // Calls standard method
            return parent::sendPerSubscriber($count, $additionalVariables);
        }
        return $this;
    }

    /**
     * Adds customer info to select
     *
     * @return Mage_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function addCustomerInfo($collection, $linkTable = "link") {
        $adapter = $collection->getConnection();
        $customer = Mage::getModel('customer/customer');
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');
        $middlename = $customer->getAttribute('middlename');
        $prefix = $customer->getAttribute('prefix');
        $suffix = $customer->getAttribute('suffix');
        $dob = $customer->getAttribute('dob');
        $gender = $customer->getAttribute('gender');
        $groupTable = $collection->getTable('customer/customer_group');
        $customerTable = $collection->getTable('customer/entity');

        $genderOptionTable = $collection->getTable('eav/attribute_option_value');

        $collection->getSelect()
            ->joinLeft(
                    array('uk' => $collection->getTable('contactlab_subscribers/uk')),
                            'uk.customer_id = ' . $linkTable . '.customer_id',
                            array('uk' => 'entity_id'))
            ->joinLeft(
                array('customer_table' => $customerTable),
                'customer_table.entity_id = ' . $linkTable . '.customer_id',
                array('email')
            )
            ->joinLeft(
                array('customer_lastname_table' => $lastname->getBackend()->getTable()),
                $adapter->quoteInto('customer_lastname_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_lastname_table.attribute_id = ?', (int) $lastname->getAttributeId()),
                array('customer_lastname' => 'value')
            )
            ->joinLeft(
                array('customer_firstname_table' => $firstname->getBackend()->getTable()),
                $adapter->quoteInto('customer_firstname_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_firstname_table.attribute_id = ?', (int) $firstname->getAttributeId()),
                array('customer_firstname' => 'value')
            )
            ->joinLeft(
                array('customer_middlename_table' => $middlename->getBackend()->getTable()),
                $adapter->quoteInto('customer_middlename_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_middlename_table.attribute_id = ?', (int) $middlename->getAttributeId()),
                array('customer_middlename' => 'value')
            )
            ->joinLeft(
                array('customer_prefix_table' => $prefix->getBackend()->getTable()),
                $adapter->quoteInto('customer_prefix_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_prefix_table.attribute_id = ?', (int) $prefix->getAttributeId()),
                array('customer_prefix' => 'value')
            )
            ->joinLeft(
                array('customer_suffix_table' => $suffix->getBackend()->getTable()),
                $adapter->quoteInto('customer_suffix_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_suffix_table.attribute_id = ?', (int) $suffix->getAttributeId()),
                array('customer_suffix' => 'value')
            )
            ->joinLeft(
                array('customer_dob_table' => $dob->getBackend()->getTable()),
                $adapter->quoteInto('customer_dob_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_dob_table.attribute_id = ?', (int) $dob->getAttributeId()),
                array('customer_dob' => 'value')
            )
            ->joinLeft(
                array('customer_gender_table' => $gender->getBackend()->getTable()),
                $adapter->quoteInto('customer_gender_table.entity_id = ' . $linkTable . '.customer_id
                 AND customer_gender_table.attribute_id = ?', (int) $gender->getAttributeId()),
                array()
            )
            ->joinLeft(
                array('customer_gender_descr_table' => $genderOptionTable),
                'customer_gender_descr_table.option_id = customer_gender_table.value AND customer_gender_descr_table.store_id = 0',
                array('substr(customer_gender, 1, 1)' => 'value')
            )
            ->joinLeft(
                array('customer_group_table' => $groupTable),
                'customer_group_table.customer_group_id = customer_table.group_id',
                array('customer_group' => 'customer_group_code')
            );

        return $this;
    }


    /**
     * Send XML Delivery.
     *
     * @return void
     */
    public function sendXmlDelivery() {
        if ($this->getQueueStatus() != self::STATUS_SENDING
           && ($this->getQueueStatus() != self::STATUS_NEVER && $this->getQueueStartAt())) {
            return $this;
        }

        $collection = $this->getSubscribersCustomerCollection();
        if ($collection->getSize() == 0) {
            $this->_finishQueue();
            return $this;
        }

        $collection->addFieldToFilter('link.letter_sent_at', array('null' => 1));
        $this->addCustomerInfo($collection);

        /* @var $xmlDelivery Contactlab_Template_Model_Newsletter_XmlDelivery */
        $rv = $this->getXmlDelivery()
            ->setTask($this->getTask())
            ->setStoreId($this->getStoreId())
            ->setQueueId($this->getQueueId())
            ->setTemplate($this->getTemplate())
            ->setSourceCollection($collection)
            ->send();
        if ($this->getXmlDelivery()
                ->getUploader()->useLocalServer()) {
            // Local upload, wont check return code, finish queue.
            $this->finishQueueAndLinks();
        }
        return $rv;
    }

    /**
     * Get Xml Delivery Instance
     * @return Contactlab_Template_Model_Newsletter_XmlDelivery
     */
    public function getXmlDelivery() {
        if ($this->hasXmlDelivery()) {
            return $this->getData('xml_delivery');
        }
        /* @var $xmlDelivery Contactlab_Template_Model_Newsletter_XmlDelivery */
        $xmlDelivery = Mage::getModel('contactlab_template/newsletter_xmlDelivery');
        $this->setXmlDelivery($xmlDelivery);
        return $xmlDelivery;
    }
    
    /**
     * Get subscriber
     * @return Varien_Data_Collection_Db
     */
    public function getSubscribersCustomerCollection() {
        if (is_null($this->_subscribersCustomerCollection)) {
            $this->_subscribersCustomerCollection = Mage::getResourceModel('newsletter/queue_collection')
                    ->addFieldToFilter("queue_id", $this->getQueueId());
            $this->_subscribersCustomerCollection->getSelect()
                    ->join(array('link' => $this->_subscribersCustomerCollection->getTable('newsletter/queue_link')),
                        'main_table.queue_id = link.queue_id',
                        array('letter_sent_at', 'product_ids', 'customer_id' => 'customer_id'));
            /* @var $helper Contactlab_Commons_Helper_Data */
            /*
            $helper = Mage::helper("contactlab_commons");
            $helper->logWarn($this->_subscribersCustomerCollection
                    ->getSelect()->assemble());
            */
        }
        return $this->_subscribersCustomerCollection;
    }

    /**
     * Finish queue and links.
     *
     * @return void
     */
    public function finishQueueAndLinks() {
        $this->_finishQueue();
        $this->_finishQueueLinks();

        return $this;
    }

    /**
     * Finish queue links.
     *
     * @return void
     */
    public function _finishQueueLinks() {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('newsletter/queue_link');
        $query = "update {$table} set letter_sent_at = utc_timestamp() where queue_id = " . (int) $this->getQueueId();
        $writeConnection->query($query);
    }
}

