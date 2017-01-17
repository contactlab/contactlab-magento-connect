<?php

/**
 * Export subscribers.
 * @property string limiter
 * @property bool found
 * @property int customers
 * @property int newsletterSubscribers
 * @property int deleted
 * @property mixed exportPolicy
 * @property Contactlab_Subscribers_Helper_Exporter helper
 * @property Mage_Core_Model_Resource $resource
 * @property string ukQuery
 * @property array customerAttributes
 * @property array addressAttributes
 * @property Mage_Customer_Model_Customer customerModel
 * @property Mage_Customer_Model_Address addressModel
 * @property array statsAttributesMap
 * @property array stores
 * @property array fAttributesMap
 * @property array fAddressAttributes
 * @property array deletedEntities
 */
class Contactlab_Subscribers_Model_Exporter_Subscribers extends Contactlab_Commons_Model_Exporter_Abstract {
    /**
     * Get xml object.
     */
    protected function writeXml() {

        $this->limiter = '###';
        $this->found = false;

        $this->customers = 0;
        $this->newsletterSubscribers = 0;
        $this->deleted = 0;

        $this->exportPolicy = $this->getTask()->getConfig('contactlab_subscribers/global/export_policy');
        $this->helper = Mage::helper("contactlab_subscribers/exporter");
        $this->resource = Mage::getSingleton('core/resource');

        $this->ukQuery = "";


        Mage::helper("contactlab_commons")->enableDbProfiler();

        if ($this->_mustResetExportDates()) {
            $this->_resetExportDates();
        }

        $this->customerAttributes = $this->helper->getAttributesCodesForEntityType('customer');
        $this->addressAttributes = $this->helper->getAttributesCodesForEntityType('customer_address');

        $this->customerModel = Mage::getModel('customer/customer');
        $this->addressModel = Mage::getModel('customer/address');

        $this->statsAttributesMap = $this->helper->getStatsAttributesMap();
        $this->stores = $this->_loadStores();

        // Define customer collection
        $attributesCustomer = $this->helper->getAttributesForEntityType('customer',
                array_keys($this->helper->getAttributesMap($this->getTask())));
        $this->fAttributesMap = array_flip($this->helper->getAttributesMap($this->getTask()));
        $this->fAddressAttributes = array_flip($this->helper->getAddressesAttributesMap());

        $counter = 0;
        $start = microtime(true);
        $max = $this->_createCounterSubscribersInCustomersCollection()->getSize();
        $this->getTask()->setMaxValue($max);
        Mage::helper("contactlab_commons")->logNotice(sprintf("Counting time: %0.4f", microtime(true) - $start));

        $customKeys = array();
        for ($ic = 1; $ic < 8; ++$ic) {
            if ($this->getTask()->getConfigFlag("contactlab_subscribers/custom_fields/enable_field_" . $ic)) {
                $customKeys[] = $this->getTask()->getConfig("contactlab_subscribers/custom_fields/field_" . $ic);
            }
        }

        $start = microtime(true);

        $limit = 50000;
        $page = 1;
        $preFilled = array_fill_keys(array_values($this->fAttributesMap), '');
        $this->_addAddressFields($preFilled);
        while (true) {
            $subscribersInCustomers = $this->_createSubscribersInCustomersCollection($attributesCustomer);
            $subscribersInCustomers->getSelect()->limitPage($page, $limit);
            
            Mage::helper("contactlab_commons")->logDebug($subscribersInCustomers->getSelect()->assemble());
            $found = false;
            while ($item = $subscribersInCustomers->fetchItem()) {
                $toFill = array();
                $found = true;
                $counter++;
                $toFill['is_customer'] = 1;
                if (!$item->hasData('uk')) {
                    $msg = sprintf("FATAL ERROR, %s subscriber has no UK record!", $item->getData('email'));
                    $this->getTask()->addEvent($msg, true);
                    throw new Exception($msg);
                }
                $toFill['entity_id'] = $item->getData('uk');

                $toFill = array_merge($toFill, $preFilled);
                $toFill['email'] = $item->getData('email');
                
                $this->_setAttributeKeys($toFill, $item);
                $this->_setAddressesAttributeKeys($toFill, $item);

                $this->_fillStoreAttributes($toFill, $item);
                foreach ($this->statsAttributesMap as $k => $v) {
                    $toFill[$k] = $item->getData($v);
                }
                $this->_fillCustomerGroupAttributes($toFill, $item);
                $this->_manageCustomerClsFlag($toFill, $item);
                /** Custom rispetto alla versione originale del modulo */
                $this->customizeInfoCustomer($toFill, $item);

                foreach ($customKeys as $icKey) {
                    if (empty($icKey)) {
                        continue;
                    }
                    $icValue = $item->getData($icKey);
                    if (isset($toFill[$icKey]) && empty($toFill[$icKey]) && !empty($icValue)) {
                        $toFill[$icKey] = $icValue;
                    }
                }
                $this->found = true;
                $this->customers++;
                $writer = new XMLWriter();
                $writer->openMemory();
                $writer->setIndent(true);
                $writer->startElement("RECORD");
                $writer->writeAttribute('ACTION', 'U');
                foreach ($toFill as $k => $v) {
                	                	 
                    if (empty($k)) {
                        continue;
                    }
                    if ($k !== $this->getSubscribedFlagName()) {
                        $k = strtoupper($this->getOutputTagName($k));
                    }
                    $writer->writeElement($k, $v);
                }
                $writer->endElement();
                gzwrite($this->gz, $writer->outputMemory());

                if ($counter % 2000 == 0) {
                    Mage::helper("contactlab_commons")->logNotice(sprintf("Exporting %6s / %-6s", $counter, $max));
                    $this->getTask()->setProgressValue($counter);
                }
            }
            $this->_setUkIsExported();
            if (!$found) {
                break;
            }
            $page++;
        }
        Mage::helper("contactlab_commons")->logNotice(sprintf("Loop time: %0.4f", microtime(true) - $start));

        $this->_addNotCustomerRecords();
        $this->_addDeletedRecords();

        $this->_setUkIsExported();

        Mage::helper("contactlab_commons")->flushDbProfiler();
        if (!$this->found) {
            $this->getTask()->addEvent("No record exported or deleted");
        } else {
            if ($this->customers > 0) {
                $this->getTask()->addEvent(sprintf("%d customers exported", $this->customers));
            }
            if ($this->newsletterSubscribers > 0) {
                $this->getTask()->addEvent(sprintf("%d newsletter subscribers exported", $this->newsletterSubscribers));
            }
            if ($this->deleted > 0) {
                $this->getTask()->addEvent(sprintf("%d record deleted", $this->deleted));
            }
        }
    }

    /**
     * Create subscribers in customers collection.
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributesCustomer
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    private function _createSubscribersInCustomersCollection(
        Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributesCustomer)
    {
        /**
         * @var $subscribersInCustomers Mage_Customer_Model_Resource_Customer_Collection
         */
        $rv = Mage::getModel('customer/customer')->getCollection();
//        $w = 'subscriber_status = ' . Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
//        $w = $this->_manageExportPolicySql($w, array(
//            'e#1' => 'updated_at',
//            'e#2' => 'created_at',
//            $this->resource->getTableName('newsletter_subscriber') => 'last_updated_at'
//        ));


//        if (!$this->_mustExportNotSubscribed()) {
//            $rv->getSelect()
//                ->where('e.entity_id in (select customer_id from ' . $this->resource->getTableName('newsletter_subscriber')
//                . ' where ' . $w . ')');
//        }

        // Fills array of types with attributes id array
        foreach ($this->helper->fillBackendTypesFromArray($attributesCustomer) as $backendType => $ids) {
            $alias = 'ce' . $backendType;
            $w = 'e.entity_id = ' . $alias . '.entity_id and length(' . $alias . '.value) > 0 and '
                    . $alias . '.attribute_id IN (' . implode(',', $ids) . ')';
            $rv->getSelect()->joinLeft(
                array(
                    $alias => $this->resource->getTableName('customer_entity_' . $backendType)),
                        $w, array($backendType . '_value' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT CONCAT('
                    . $alias . ".attribute_id, '_', " .$alias. ".value) SEPARATOR '" . $this->limiter . "')")));
        }

        // Stats
        $this->_addStatsToSelect($rv);
        $this->_addAddressesToSelect($rv);
        $this->_addCustomerGroupToSelect($rv);
        $this->_manageExportPolicy($rv,
            array(
                'e' => array('created_at', 'updated_at'),
                $this->resource->getTableName('newsletter_subscriber') => 'last_updated_at',
            ));
        $this->_addCustomerExportCollection($rv);
        $this->_addSubscriberFields($rv);

        $rv->getSelect()->group('e.entity_id');

        return $rv;
    }

    /**
     * Create count subscribers in customers collection.
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    private function _createCounterSubscribersInCustomersCollection()
    {
        /**
         * @var $subscribersInCustomers Mage_Customer_Model_Resource_Customer_Collection
         */
        $rv = Mage::getModel('customer/customer')->getCollection();
//        $w = 'subscriber_status = ' . Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
//        $w = $this->_manageExportPolicySql($w, array(
//            'e#1' => 'updated_at',
//            'e#2' => 'created_at',
//            $this->resource->getTableName('newsletter_subscriber') => 'last_updated_at'
//        ));

//        if (!$this->_mustExportNotSubscribed()) {
//            $rv->getSelect()
//                ->where('e.entity_id in (select customer_id from ' . $this->resource->getTableName('newsletter_subscriber')
//                . ' where ' . $w . ')');
//        }

        $this->_manageExportPolicy($rv,
            array(
                'e' => array('created_at', 'updated_at'),
                $this->resource->getTableName('newsletter_subscriber') => 'last_updated_at',
            ));
        $this->_addCustomerExportCollection($rv);
        return $rv;
    }

    /**
     * Called after the export.
     */
    public function afterFileCopy() {
        $this->helper->soapCallAfterExport($this->getTask());
    }

    /** Not customer records. */
    private function _addNotCustomerRecords() {
        Mage::helper("contactlab_commons")->logDebug("_addNotCustomerRecords");
        $preFilled = array_fill_keys(array_values($this->fAttributesMap), '');
        $this->_addAddressFields($preFilled);
        $subscribersNotInCustomers = $this->_createSubscribersNotInCustomers();
        $customKeys = array();
        for ($ic = 1; $ic < 8; ++$ic) {
            if ($this->getTask()->getConfigFlag("contactlab_subscribers/custom_fields/enable_field_" . $ic)) {
                $customKeys[] = $this->getTask()->getConfig("contactlab_subscribers/custom_fields/field_" . $ic);
            }
        }
        $counter = 0;
        $max = $subscribersNotInCustomers->getSize();
        $this->getTask()->setMaxValue($max);

        $limit = 200000;
        $page = 1;
        while (true) {
            $subscribersNotInCustomers = $this->_createSubscribersNotInCustomers();
            $subscribersNotInCustomers->getSelect()->limitPage($page, $limit);
            Mage::helper("contactlab_commons")->logDebug($subscribersNotInCustomers->getSelect()->assemble());
            $found = false;

            while ($item = $subscribersNotInCustomers->fetchItem()) {
                $counter++;
                $found = true;
                $toFill1 = array();

                $toFill1['is_customer'] = 0;
                if (!$item->hasData('uk')) {
                    $msg = sprintf("FATAL ERROR, %s customer has no UK record!", $item->getData('subscriber_email'));
                    $this->getTask()->addEvent($msg, true);
                    throw new Exception($msg);
                }
                $toFill1['entity_id'] = $item->getData('uk');

                $toFill = array_merge($toFill1, $preFilled);
                $toFill['email'] = $item->getData('subscriber_email');
                $this->_manageNewsletterClsFlag($toFill, $item);
                $this->_fillStoreAttributes($toFill, $item);
                $this->_fillNewsletterAttributes($toFill, $item);
                /** Custom rispetto alla versione originale del modulo */
                $this->customizeInfoSubscriber($toFill, $item);

                foreach ($customKeys as $icKey) {
                    if (empty($icKey)) {
                        continue;
                    }
                    $icValue = $item->getData($icKey);
                    if (isset($toFill[$icKey]) && empty($toFill[$icKey]) && !empty($icValue)) {
                        $toFill[$icKey] = $icValue;
                    }
                }

                $this->found = true;
                $this->newsletterSubscribers++;
                $writer = new XMLWriter();
                $writer->openMemory();
                $writer->setIndent(true);
                $writer->startElement("RECORD");
                $writer->writeAttribute('ACTION', 'U');
                foreach ($toFill as $k => $v) {
                    if (empty($k)) {
                        continue;
                    }
                    if ($k !== $this->getSubscribedFlagName()) {
                        $k = strtoupper($this->getOutputTagName($k));
                    }
                    $writer->writeElement($k, $v);
                }
                $writer->endElement();
                gzwrite($this->gz, $writer->outputMemory());
                if ($counter % 2000 == 0) {
                    Mage::helper("contactlab_commons")->logNotice(sprintf("Exporting %6s / %-6s", $counter, $max));
                    $this->getTask()->setProgressValue($counter);
                }
            }
            $this->_setUkIsExported();
            if (!$found) {
                break;
            }
            $page++;
        }
    }


    /**
     * Create subscribers not in customers.
     * @return Contactlab_Template_Model_Resource_Newsletter_Subscriber_Collection
     */
    private function _createSubscribersNotInCustomers()
    {
        /** @var $rv Contactlab_Template_Model_Resource_Newsletter_Subscriber_Collection */
        $rv = Mage::getModel('contactlab_subscribers/uk')->getCollection();
        $rv->addFieldToSelect(
            array('uk' => 'entity_id', 'is_exported' => 'is_exported')
        );
        $this->_addSubscriberFields($rv, 'main_table');
        $rv->getSelect()->joinLeft(
            array('native_nl' => $this->resource->getTableName('newsletter/subscriber')),
            'native_nl.subscriber_id = main_table.subscriber_id');
        $this->_manageExportPolicy($rv,
            array('native_nl' => 'last_updated_at'));
        if (!$this->_mustExportNotSubscribed()) {
            $rv->addFieldToFilter('native_nl.subscriber_status', Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);            
        }
        $rv->getSelect()->where('main_table.customer_id is null or main_table.customer_id = 0');
        return $rv;
    }

    
    /**
     * Add delete records.
     */
    private function _addDeletedRecords() {
        /** @var $deletedEntities Contactlab_Commons_Model_Resource_Deleted_Collection */
        $deletedEntities = Mage::getModel('contactlab_commons/deleted')
                ->getCollection()
                ->addFieldToFilter('task_id', array('null' => true));

        $counter = 0;
        // FIXME TODO count vs getSize
        $max = $deletedEntities->count();

        Mage::helper("contactlab_commons")->logDebug($deletedEntities->getSelect()->assemble());
        $this->deletedEntities = array();
        $preFilled = array_fill_keys(array_keys($this->fAttributesMap), '');
        $this->_addAddressFields($preFilled);

        /** @var $deletedEntity Contactlab_Commons_Model_Deleted */
        while ($deletedEntity = $deletedEntities->fetchItem()) {
            $counter++;
            $toFill1 = array();

            $this->found = true;
            $this->deleted++;

            $toFill1['is_customer'] = $deletedEntity->getIsCustomer();
            $toFill1['entity_id'] = $deletedEntity->getEntityId();
            $toFill = array_merge($toFill1, $preFilled);
            $toFill['email'] = $deletedEntity->getEmail();
            /** Custom rispetto alla versione originale del modulo */
            $this->customizeInfoDeleted($toFill, $deletedEntity);


            $writer = new XMLWriter();
            $writer->openMemory();
            $writer->setIndent(true);
            $writer->startElement("RECORD");
            $writer->writeAttribute('ACTION', 'D');
            foreach ($toFill as $k => $v) {
                if ($k !== $this->getSubscribedFlagName()) {
                    $k = strtoupper($k);
                }
                $writer->writeElement($k, $v);
            }
            $writer->endElement();
            gzwrite($this->gz, $writer->outputMemory());

            $deletedEntity->setTaskId($this->getTask()->getTaskId())->save();
            if ($counter % 500 == 0) {
                Mage::helper("contactlab_commons")->logNotice(sprintf("Exporting deleted %6s / %-6s", $counter, $max));
            }
        }
    }

    /**
     * Set addresses attribute keys.
     * @param array $toFill
     * @param Varien_Object $data
     */
    private function _setAddressesAttributeKeys(array &$toFill, Varien_Object $data) {
        $this->_setAddressAttributeKeys($toFill, $data, "shipping");
        $this->_setAddressAttributeKeys($toFill, $data, "billing");
    }

    /**
     * Do export address with type.
     * @param string $addressType
     * @return bool
     */
    private function _mustExportAddress($addressType) {
        return $this->getTask()->getConfigFlag("contactlab_subscribers/global/export_" . $addressType . "_address");
    }

    /**
     * Set address attribute keys.
     * @param array $toFill
     * @param Varien_Object $data
     * @param $addressType
     */
    private function _setAddressAttributeKeys(array &$toFill, Varien_Object $data, $addressType) {
        if (!$this->_mustExportAddress($addressType)) {
            return;
        }
        $entityTypes = array('int', 'varchar', 'text', 'decimal', 'datetime');
        foreach ($entityTypes as $type) {
            if (!array_key_exists($addressType . '_' . $type . '_value', $data->getData())) {
                return;
            }
            $values = explode($this->limiter, $data->getData($addressType . '_' . $type . '_value'));
            foreach ($values as $key) {
                $pos = strpos($key, '_');
                if ($pos === false) {
                    continue;
                }
                $k = substr($key, 0, $pos);
                $v = substr($key, $pos + 1);
                if (!array_key_exists($k, $this->addressAttributes)) {
                    continue;
                }
                $toFill[$addressType . '_' . $this->fAddressAttributes[$this->addressAttributes[$k]]] =
                        $this->helper->decode($this->addressModel, 'address', $this->addressAttributes[$k], $v);
            }
        }
    }

    /**
     * Set attribute keys.
     * @param array $toFill
     * @param Varien_Object $data
     */
    private function _setAttributeKeys(array &$toFill, Varien_Object $data) {
        $entityTypes = array('int', 'varchar', 'text', 'decimal', 'datetime');
        foreach ($entityTypes as $type) {
            if (!array_key_exists($type . '_value', $data->getData())) {
                continue;
            }
            $values = explode($this->limiter, $data->getData($type . '_value'));
            foreach ($values as $key) {
                $pos = strpos($key, '_');
                if ($pos === false) {
                    continue;
                }
                $k = substr($key, 0, $pos);
                $v = substr($key, $pos + 1);
                if (!array_key_exists($k, $this->customerAttributes)) {
                    continue;
                }
                // use flipped array for cstm attributes
                //$flip = array_flip($this->fAttributesMap);
                //$toFill[$this->fAttributesMap[$this->customerAttributes[$k]]] =
//                $toFill[$flip[$this->customerAttributes[$k]]] =
                $toFill[$this->customerAttributes[$k]] =
                    $this->helper->decode($this->customerModel, 'customer', $this->customerAttributes[$k], $v);
            }
        }

    }

    /**
     * Add store, group and website attributes.
     * @param array $toFill
     * @param Varien_Object $item
     */
    private function _fillStoreAttributes(array &$toFill, Varien_Object $item) {
        $store = $this->stores[$item->getData('store_id')];
        foreach (array('store_id', 'store_name', 'website_id', 'website_name',
            'group_id', 'group_name', 'lang') as $k) {
            $toFill[$k] = $store[$k];
        }
    }

    private function _fillCustomerGroupAttributes(array &$toFill, Varien_Object $item)
    {
        $toFill['customer_group_id'] = $item->getData('customer_group_id');
        $toFill['customer_group_name'] = $item->getData('customer_group_name');        
    }

    /**
     * Fill Newsletter attributes fields.
     * @param array $toFill
     * @param Varien_Object $item
     */
    private function _fillNewsletterAttributes(array &$toFill, Varien_Object $item)
    {
        foreach ($this->helper->getSubscriberToCustomerAttributeMap() as $k => $v) {
            if (!$this->_doManageAddressAttribute($v)) {
                continue;
            }
            $toFill[$v] = $item->getData($k);
        }
    }

    /**
     * Add stats to collection.
     * @param Varien_Data_Collection_Db $collection
     */
    private function _addStatsToSelect(Varien_Data_Collection_Db $collection) {
        $collection->getSelect()->joinLeft(
                    array('stats' => $this->resource->getTableName('contactlab_subscribers/stats')),
                            'stats.customer_id = e.entity_id', $this->statsAttributesMap);
    }

    /**
     * Add customer export to collection.
     * @param Varien_Data_Collection_Db $collection
     */
    private function _addCustomerExportCollection(Varien_Data_Collection_Db $collection) {
        if (!$this->_mustExportNotSubscribed()) {
            $w = 'subscriber_status = ' . Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
            $w = $this->_manageExportPolicySql($w, array(
                'e#1' => 'updated_at',
                'e#2' => 'created_at',
                $this->resource->getTableName('newsletter_subscriber') => 'last_updated_at'
            ));

            $collection->getSelect()->joinInner(
                array('newsletter_subscriber' => $this->resource->getTableName('newsletter/subscriber')),
                'newsletter_subscriber.customer_id = e.entity_id AND ' . $w,
                array('subscriber_status' => 'subscriber_status', 'subscriber_created_at'=>'created_at', 'last_subscribed_at' => 'last_subscribed_at', 'last_updated_at' => 'last_updated_at'));
        }else{
            $collection->getSelect()->joinLeft(
                array('newsletter_subscriber' => $this->resource->getTableName('newsletter/subscriber')),
                'newsletter_subscriber.customer_id = e.entity_id',
                array('subscriber_status' => 'subscriber_status', 'subscriber_created_at'=>'created_at', 'last_subscribed_at' => 'last_subscribed_at', 'last_updated_at' => 'last_updated_at'));
        }

        $collection->getSelect()->joinLeft(
                    array('uk' => $this->resource->getTableName('contactlab_subscribers/uk')),
                            'uk.customer_id = e.entity_id',
                            array('uk' => 'entity_id', 'is_exported' => 'is_exported'));
    }

    /**
     * Add newsletter subscriber export to collection.
     * @param Varien_Data_Collection_Db $collection
     */
    private function _addNewsletterSubscriberExportCollection(Varien_Data_Collection_Db $collection) {
        $collection->getSelect()->joinLeft(
                    array('uk' => $this->resource->getTableName('contactlab_subscribers/uk')),
                            'uk.subscriber_id = main_table.subscriber_id',
                            array('uk' => 'entity_id', 'is_exported' => 'is_exported'));
    }

    /**
     * Add export policy to sql statement.
     * @param Varien_Data_Collection_Db $collection
     * @param array $fields
     */
    private function _manageExportPolicy(Varien_Data_Collection_Db $collection,
            array $fields) {
        if ($this->exportPolicy != '2') {
            // Ok, will export everything
            return;
        }
        $lastExport = $this->getTask()->getConfig('contactlab_subscribers/global/last_export');
        if (empty($lastExport)) {
            // Ok, will export everything, no last import saved.
            return;
        }
        $clause = '';
        //$lastExport = Mage::getModel("core/date")->gmtDate(null, $lastExport);

        foreach ($fields as $table => $field) {
            if (!is_array($field)) {
                $field = array($field);
            }
            foreach ($field as $item) {
                if (!empty($clause)) {
                    $clause .= ' or ';
                }
                $clause .= sprintf("%s.%s >= '%s'", $table, $item, $lastExport);
                if (preg_match("/.*newsletter_subscriber.*/", $table)) {
                    $clause .= sprintf(" or (%s.%s is null and %s.subscriber_id is not null)", $table, $item, $table);
                } else {
                    $clause .= sprintf(" or %s.%s is null", $table, $item);
                }
            }
        }
        $collection->getSelect()->where($clause);
    }

    /**
     * Add export policy to sql statement.
     * @param string $w
     * @param array $fields
     * @return string
     */
    private function _manageExportPolicySql($w, array $fields) {
        if ($this->exportPolicy != '2') {
            // Ok, will export everything
            return $w;
        }
        $lastExport = $this->getTask()->getConfig('contactlab_subscribers/global/last_export');
        if (empty($lastExport)) {
            // Ok, will export everything, no last import saved.
            return $w;
        }
        // $lastExport = Mage::getModel("core/date")->gmtDate(null, $lastExport);

        $w .= ' and (';
        $first = true;
        foreach ($fields as $table => $field) {
            $table = preg_replace('|#.*|', '', $table);
            if (!is_array($field)) {
                $field = array($field);
            }
            if (!$first) {
                $w .= ' and ';
            }
            foreach ($field as $item) {
                $w .= sprintf("%s.%s >= '%s'", $table, $item, $lastExport);
                if (preg_match("/.*newsletter_subscriber.*/", $table)) {
                    $w .= sprintf(" or (%s.%s is null and %s.subscriber_id is not null)", $table, $item, $table);
                } else {
                    $w .= sprintf(" or %s.%s is null", $table, $item);
                }
            }
            $first = false;
        }
        $w .= ')';
        return $w;
    }

    /**
     * Add addresses to select
     * @param Varien_Data_Collection_Db $collection
     */
    private function _addAddressesToSelect(Varien_Data_Collection_Db $collection) {
        $this->_addAddressToSelect($collection, "shipping");
        $this->_addAddressToSelect($collection, "billing");
    }

    /**
     * Add address to select.
     * @param Varien_Data_Collection_Db $collection
     * @param string $type
     */
    private function _addAddressToSelect(Varien_Data_Collection_Db $collection, $type) {
        if (!$this->getTask()->getConfigFlag("contactlab_subscribers/global/export_" . $type . "_address")) {
            return;
        }
        $collection->getSelect()->joinLeft(
                    array($type . '_addr' => $this->resource->getTableName('customer_entity_int')),
                            $type . '_addr.entity_id = e.entity_id and ' . $type . '_addr.attribute_id = '
                            . $this->helper->getAttributeId('customer', 'default_' . $type),
                            array('default_' . $type => 'value'));

        // Fills array of types with attributes id array
        $attributesAddress = $this->helper->getAttributesForEntityType('customer_address',
                array_values($this->helper->getAddressesAttributesMap()));
        foreach ($this->helper->fillBackendTypesFromArray($attributesAddress) as $backendType => $ids) {
            $alias = 'ce' . $type . $backendType;
            $w = $type . '_addr.value = ' . $alias . '.entity_id and length(' . $alias . '.value) > 0 and '
                    . $alias . '.attribute_id IN (' . implode(',', $ids) . ')';
            $collection->getSelect()->joinLeft(
                array(
                    $alias => $this->resource->getTableName('customer_address_entity_' . $backendType)),
                        $w, array($type . '_' . $backendType . '_value' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT CONCAT('
                    . $alias . ".attribute_id, '_', " .$alias. ".value) SEPARATOR '" . $this->limiter . "')")));
        }
    }

    /**
     * Remove deleted entity.
     * @throws Exception
     */
    private function _removeDeletedEntity() {
        $toDelete = Mage::getModel('contactlab_commons/deleted')
                ->getCollection()
                ->addFieldToFilter('task_id', $this->getTask()->getTaskId());

        $counter = 0;
        $max = $toDelete->count();

        Mage::helper("contactlab_commons")->logDebug($toDelete->getSelect()->assemble());

        foreach ($toDelete as $item) {
            $counter++;
            $item->delete();
            if ($counter % 500 == 0) {
                Mage::helper("contactlab_commons")->logNotice(sprintf("Deleted %6s / %-6s", $counter, $max));
            }
        }
    }

    /**
     * Load stores.
     * @return array
     */
    private function _loadStores() {
        $rv = array();
        $websiteTable = $this->resource->getTableName('core/website');
        $storeGroupTable = $this->resource->getTableName('core/store_group');

        /** @var Mage_Core_Model_Resource_Store_Collection $stores */
        $stores = Mage::getModel('core/store')
                ->getCollection()->setLoadDefault(true);
        $stores->addFieldToSelect('*')->getSelect()
            ->join(array('core_website' => $websiteTable),
                'main_table.website_id = ' . 'core_website.website_id',
                array('website_name' => 'core_website.name'))
            ->join(array('core_store_group' => $storeGroupTable),
                'main_table.group_id = ' . 'core_store_group.group_id',
                array('group_name' => 'core_store_group.name'));        
        /** @var $store Mage_Core_Model_Store */
        foreach ($stores as $store) {
            $storeId = $store->getData('store_id');
            $rv[$storeId] = array(
                'store_id' => $storeId,
                'website_id' => $store->getData('website_id'),
                'group_id' => $store->getData('group_id'),
                'store_name' => $store->getData('mame'),
                'website_name' => $store->getData('website_name'),
                'group_name' => $store->getData('group_name'),
                'lang' => $store->getConfig("general/locale/code"),
            );
        }
        return $rv;
    }

    /**
     * Do I have to export all contacts? Or only subscribed ones?
     * @return bool
     */
    private function _mustExportNotSubscribed() {
        return $this->getTask()->getConfigFlag("contactlab_subscribers/global/export_not_subscribed");
    }

    /**
     * Do I have to reset export dates before export?
     * @return bool
     */
    private function _mustResetExportDates() {
        return $this->getTask()->getConfigFlag("contactlab_subscribers/global/reset_export_dates_before_next_export");
    }

    /**
     * Reset reset export flg.
     */
    private function _resetMustResetExportDatesFlag() {
        Mage::helper("contactlab_commons")->logDebug("Reset reset_export_dates_before_next_export flag");
        Mage::getConfig()
            ->saveConfig('contactlab_subscribers/global/reset_export_dates_before_next_export', '0');
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    private function _resetExportDates() {
        $tableName = $this->resource->getTableName('contactlab_subscribers/uk');
        $this->_query("update $tableName set is_exported = 0;");
    }

    /**
     * Run custom query.
     * @param $sql
     * @return Zend_Db_Statement_Interface
     */
    private function _query($sql) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        return $write->query($sql);
    }


    /** Get subscription flag name. */
    public function getSubscribedFlagName() {
        return $this->getTask()->getConfig("contactlab_subscribers/global/subscribed_flag_name");
    }

    /**
     * Manage if include CLS tag and his value.
     * Fill the persistence table.
     * @param array $toFill
     * @param Varien_Object $item
     */
    private function _manageCustomerClsFlag(array &$toFill, Varien_Object $item) {
        /*  1 = Subscribed
            0 = Not subscribed
           -1 = Unsubscribed
        */
        if ($this->_manageClsFlag($toFill, $item)) {
            if ($this->ukQuery != '') {
                $this->ukQuery .= ', ';
            }
            $this->ukQuery .= $item->getData('uk');
        }
    }

    /**
     * Manage if include CLS tag and his value.
     * Fill the persistence table.
     * @param array $toFill
     * @param Varien_Object $item
     */
    private function _manageNewsletterClsFlag(array &$toFill, Varien_Object $item) {
        if ($this->_manageClsFlag($toFill, $item, true)) {
            if ($this->ukQuery != '') {
                $this->ukQuery .= ', ';
            }
            $this->ukQuery .= $item->getData('uk');
        }
    }

    /**
     * Manage if include CLS tag and his value.
     * Fill the persistence table.
     * @param array $toFill
     * @param Varien_Object $item
     * @param bool $force
     * @return bool
     */
    private function _manageClsFlag(array &$toFill, Varien_Object $item, $force = false) {
        // If processing record has (first) ExportDate, return.
        if ($item->hasData('is_exported') && $item->getData('is_exported') == 1 && !$force) {
            return false;
        }
        $toFill[$this->getSubscribedFlagName()]
            = $item->getData('subscriber_status') == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
                ? 1 : 0;
        return true;
    }

    /**
     * Is enabled.
     * @return bool
     */
    protected function isEnabled() {
        return Mage::helper("contactlab_subscribers")->isEnabled($this->getTask());
    }

    /**
     * Get file name.
     * @return string
     */
    protected function getFileName() {
        return $this->getTask()->getConfig("contactlab_subscribers/global/export_filename");
    }

    /**
     * Called after the export.
     */
    public function afterExport() {
        $this->helper->saveLastExportDatetime($this->getTask());
        $this->_removeDeletedEntity();
        if ($this->_mustResetExportDates()) {
            $this->_resetMustResetExportDatesFlag();
        }
    }

    /**
     * Set uk is exported.
     */
    private function _setUkIsExported()
    {
        if ($this->ukQuery != "") {
            $tableName = $this->resource->getTableName('contactlab_subscribers/uk');
            $this->ukQuery = "update $tableName set is_exported = 1 where entity_id in ({$this->ukQuery})";
            $this->_query($this->ukQuery);
            $this->ukQuery = "";
        }
    }

    /**
     * Add subscriber fields.
     * @param Varien_Data_Collection_Db $collection
     * @param string $table Table name for join
     * @return Varien_Db_Select
     */
    private function _addSubscriberFields(Varien_Data_Collection_Db $collection, $table = 'newsletter_subscriber')
    {
        return $collection->getSelect()->joinLeft(
                    array('fields' => $this->resource->getTableName('contactlab_subscribers/newsletter_subscriber_fields')),
                    "fields.subscriber_id = $table.subscriber_id"
        );
    }

    /**
     * Do include this field into xml element?
     * @param String $attributeCode
     * @return bool
     */
    private function _doManageAddressAttribute($attributeCode)
    {
        if (preg_match('|^billing_|', $attributeCode)) {
            return $this->_mustExportAddress('billing');
        } else if (preg_match('|^shipping|', $attributeCode)) {
            return $this->_mustExportAddress('shipping');
        }
        return true;
    }

    /**
     * Add address fields.
     * @param array $preFilled
     */
    private function _addAddressFields(array &$preFilled)
    {
        foreach (array('billing', 'shipping') as $addressType) {
            if ($this->_mustExportAddress($addressType)) {
                foreach ($this->fAddressAttributes as $k) {
                    $preFilled[$addressType . '_' . $k] = '';
                }
            }
        }
    }

    /**
     * @param Varien_Data_Collection_Db $collection
     * @return Varien_Db_Select
     */
    private function _addCustomerGroupToSelect(Varien_Data_Collection_Db $collection)
    {
        return $collection->getSelect()->joinInner(
            array(
                'customer_group' => $this->resource->getTableName('customer/customer_group')
            ),
            "customer_group.customer_group_id = e.group_id",
            array(
                'customer_group_name' => 'customer_group_code',
                'customer_group_id' => 'customer_group_id'
            )
        );
    }

    /**
     * Use destination field name for cstm fields
     * @param $k
     * @return mixed
     */
    private function getOutputTagName($k) {
        if (isset($this->fAttributesMap[$k]) && strpos($k, 'cstm') === 0) {
            return $this->fAttributesMap[$k];
        }
        return $k;
    }

    /**
     *  Custom rispetto alla versione originale del modulo
     */
    protected function customizeInfoCustomer(array &$toFill, Varien_Object $customer){
        $tCustInfo = Mage::getModel('contactlab_subscribers/exporter_subscribers_infoTransporter_customer');
        $tCustInfo->setInfo($toFill);
        $tCustInfo->setCustomer($customer);
        $tCustInfo->setIsMod(false);

        //FIX
        $toFill['created_at'] = $customer->getData('created_at');
        if($customer->getData('subscriber_created_at'))
        {
        	$createdAt = $customer->getData('created_at');
        	$subscriberCreatedAt = $customer->getData('subscriber_created_at');
        	if(strtotime($subscriberCreatedAt) < strtotime($createdAt))
        	{
        		$toFill['created_at'] = $customer->getData('subscriber_created_at');
        	}
        }
        if($customer->getData('last_subscribed_at'))
        {
        	$toFill['last_subscribed_at'] = $customer->getData('last_subscribed_at');
        }
        Mage::dispatchEvent("contactlab_export_subscribers_customer_info",array(
            'customer_info' => $tCustInfo
        ));

        if ($tCustInfo->isMod()){
            $toFill = $tCustInfo->getInfo();
        }
    }


    /**
     *  Custom rispetto alla versione originale del modulo
     */
    protected function customizeInfoDeleted(array &$toFill, Varien_Object $deleted){
        $tDeletedInfo = Mage::getModel('contactlab_subscribers/exporter_subscribers_infoTransporter_deleted');
        $tDeletedInfo->setInfo($toFill);
        $tDeletedInfo->setDeleted($deleted);
        $tDeletedInfo->setIsMod(false);

        Mage::dispatchEvent("contactlab_export_subscribers_deleted_info",array(
            'deleted_info' => $tDeletedInfo
        ));

        if ($tDeletedInfo->isMod()){
            $toFill = $tDeletedInfo->getInfo();
        }
    }


    /**
     *  Custom rispetto alla versione originale del modulo
     */
    protected function customizeInfoSubscriber(array &$toFill, Varien_Object $subscriber){
        $tSubscriberInfo = Mage::getModel('contactlab_subscribers/exporter_subscribers_infoTransporter_subscriber');
        $tSubscriberInfo->setInfo($toFill);
        $tSubscriberInfo->setSubscriber($subscriber);
        $tSubscriberInfo->setIsMod(false);       
        
        //FIX
        $toFill['created_at'] = $subscriber->getData('created_at');
        
        
        Mage::dispatchEvent("contactlab_export_subscribers_subscriber_info",array(
            'subscriber_info' => $tSubscriberInfo
        ));

        if ($tSubscriberInfo->isMod()){
            $toFill = $tSubscriberInfo->getInfo();
        }
    }
}
