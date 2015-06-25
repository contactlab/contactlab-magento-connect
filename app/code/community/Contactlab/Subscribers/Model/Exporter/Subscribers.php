<?php

/**
 * Export subscribers.
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
        $this->r = Mage::getSingleton('core/resource');

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
                array_values($this->helper->getAttributesMap($this->getTask())));
        $this->fAttributesMap = array_flip($this->helper->getAttributesMap($this->getTask()));
        $this->fAddressAttributes = array_flip($this->helper->getAddressesAttributesMap());

        $subscribersInCustomers = Mage::getModel('customer/customer')->getCollection();

        $w = 'subscriber_status = ' . Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
        $w = $this->_manageExportPolicySql($w, array(
            'e' => 'updated_at',
            'e' => 'created_at',
            $this->r->getTableName('newsletter_subscriber') => 'last_updated_at'
        ));

        if (!$this->_mustExportNotSubscribed()) {
            $subscribersInCustomers->getSelect()
                ->where('e.entity_id in (select customer_id from ' . $this->r->getTableName('newsletter_subscriber')
                . ' where ' . $w . ')');
        }

        // Fills array of types with attributes id array
        foreach ($this->helper->fillBackendTypesFromArray($attributesCustomer) as $backendType => $ids) {
            $alias = 'ce' . $backendType;
            $w = 'e.entity_id = ' . $alias . '.entity_id and length(' . $alias . '.value) > 0 and '
                    . $alias . '.attribute_id IN (' . implode(',', $ids) . ')';
            $subscribersInCustomers->getSelect()->joinLeft(
                array(
                    $alias => $this->r->getTableName('customer_entity_' . $backendType)),
                        $w, array($backendType . '_value' => 'GROUP_CONCAT(DISTINCT CONCAT('
                    . $alias . '.attribute_id, "_", '.$alias.'.value) SEPARATOR "' . $this->limiter . '")'));
        }

        // Stats
        $this->_addStatsToSelect($subscribersInCustomers);
        $this->_addAddressesToSelect($subscribersInCustomers);
        $this->_manageExportPolicy($subscribersInCustomers,
            array(
                'e' => array('created_at', 'updated_at'),
                $this->r->getTableName('newsletter_subscriber') => 'last_updated_at',
            ));
        $this->_addCustomerExportCollection($subscribersInCustomers);

        $subscribersInCustomers->getSelect()->group('e.entity_id');

        Mage::helper("contactlab_commons")->logDebug($subscribersInCustomers->getSelect()->assemble());

        $prefilled = array_fill_keys(array_keys($this->fAttributesMap), '');
        foreach (array('billing', 'shipping') as $addressType) {
            if ($this->_mustExportAddress($addressType)) {
                foreach ($this->fAddressAttributes as $k) {
                    $prefilled[$addressType . '_' . $k] = '';
                }
            }
        }

        $counter = 0;
        $start = microtime(true);
        $max = $subscribersInCustomers->count();
        $this->getTask()->setMaxValue($max);
        Mage::helper("contactlab_commons")->logNotice(sprintf("Counting time: %0.4f", microtime(true) - $start));

        $start = microtime(true);

        foreach ($subscribersInCustomers as $item) {
            $counter++;
            $toFill['is_customer'] = 1;
            if (!$item->hasUk()) {
                $msg = sprintf("FATAL ERROR, %s subscriber has no UK record!", $item->getEmail());
                $this->getTask()->addEvent($msg, true);
                throw new Exception($msg);
            }
            $toFill['entity_id'] = $item->getUk();
            $toFill = array_merge($toFill, $prefilled);
            $toFill['email'] = $item->getEmail();
            $this->_setAttributeKeys($toFill, $item);
            $this->_setAddressesAttributeKeys($toFill, $item);

            $this->_fillStoreAttributes($toFill, $item);
            foreach ($this->statsAttributesMap as $k => $v) {
                $toFill[$k] = $item->getData($v);
            }
            $this->_manageCustomerClsFlag($toFill, $item);

            $this->found = true;
            $this->customers++;
            $writer = new XMLWriter();
            $writer->openMemory();
            $writer->startElement("RECORD");
            $writer->writeAttribute('ACTION', 'U');
            foreach ($toFill as $k => $v) {
                if ($k !== $this->getSubscribedFlagName()) {
                    $k = strtoupper($k);
                }
                $writer->writeElement($k, $v);
            }
            $writer->endElement();
            gzwrite($this->gz, $writer->outputMemory());

            if ($counter % 500 == 0) {
                Mage::helper("contactlab_commons")->logNotice(sprintf("Exporting %6s / %-6s", $counter, $max));
                $this->getTask()->setProgressValue($counter);
            }
        }
        Mage::helper("contactlab_commons")->logNotice(sprintf("Loop time: %0.4f", microtime(true) - $start));

        $this->_addNotCustomerRecords();
        $this->_addDeletedRecords();

        if ($this->ukQuery != "") {
            $this->ukQuery = sprintf('update %s set is_exported = 1 where entity_id in (%s)',
                $this->r->getTableName('contactlab_subscribers/uk'),
                $this->ukQuery);
            $this->_query($this->ukQuery);
        }

        Mage::helper("contactlab_commons")->flushDbProfiler();
        if (!$this->found) {
            $this->getTask()->addEvent("No record exported or deleted", true);
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
     * Called after the export.
     */
    public function afterFileCopy() {
        $this->helper->soapCallAfterExport($this->getTask());
    }

    /** Not customer records. */
    private function _addNotCustomerRecords() {
        Mage::helper("contactlab_commons")->logDebug("_addNotCustomerRecords");
        $subscribersNotInCustomers = Mage::getModel('newsletter/subscriber')->getCollection();
        $this->_manageExportPolicy($subscribersNotInCustomers,
            array('main_table' => 'last_updated_at'));
        if (!$this->_mustExportNotSubscribed()) {
            $subscribersNotInCustomers
                ->addFieldToFilter('subscriber_status', Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
        }
        $this->_addNewsletterSubscriberExportCollection($subscribersNotInCustomers);

        $subscribersNotInCustomers
            ->getSelect()->where('main_table.customer_id is null or main_table.customer_id = 0');

        $counter = 0;


        Mage::helper("contactlab_commons")->logDebug("Prima del count, subscribersNotInCustomers");
        Mage::helper("contactlab_commons")->logDebug($subscribersNotInCustomers->getSelect()->assemble());
        $max = $subscribersNotInCustomers->count();
        $this->getTask()->setMaxValue($max);
        $prefilled = array_fill_keys(array_keys($this->fAttributesMap), '');
        foreach (array('billing', 'shipping') as $addressType) {
            if ($this->_mustExportAddress($addressType)) {
                foreach ($this->fAddressAttributes as $k) {
                    $prefilled[$addressType . '_' . $k] = '';
                }
            }
        }

        Mage::helper("contactlab_commons")->logDebug($subscribersNotInCustomers->getSelect()->assemble());

        foreach ($subscribersNotInCustomers as $item) {
            $counter++;
            $toFill = array();

            $toFill['is_customer'] = 0;
            if (!$item->hasUk()) {
                $msg = sprintf("FATAL ERROR, %s customer has no UK record!", $item->getSubscriberEmail());
                $this->getTask()->addEvent($msg, true);
                throw new Exception($msg);
            }
            $toFill['entity_id'] = $item->getUk();
            $toFill = array_merge($toFill, $prefilled);
            $toFill['email'] = $item->getSubscriberEmail();
            $this->_manageNewsletterClsFlag($toFill, $item);
            $this->_fillStoreAttributes($toFill, $item);

            $this->found = true;
            $this->newsletterSubscribers++;
            $writer = new XMLWriter();
            $writer->openMemory();
            $writer->startElement("RECORD");
            $writer->writeAttribute('ACTION', 'U');
            foreach ($toFill as $k => $v) {
                if ($k !== $this->getSubscribedFlagName()) {
                    $k = strtoupper($k);
                }
                $writer->writeElement($k, $v);
            }
            $writer->endElement();
            gzwrite($this->gz, $writer->outputMemory());
            if ($counter % 500 == 0) {
                Mage::helper("contactlab_commons")->logNotice(sprintf("Exporting %6s / %-6s", $counter, $max));
                $this->getTask()->setProgressValue($counter);
            }
        }
    }

    private function _addDeletedRecords() {
        $deletedEntities = Mage::getModel('contactlab_commons/deleted')
                ->getCollection()
                ->addFieldToFilter('task_id', array('null' => true));

        $counter = 0;
        $max = $deletedEntities->count();

        Mage::helper("contactlab_commons")->logDebug($deletedEntities->getSelect()->assemble());
        $this->deletedEntities = array();
        $prefilled = array_fill_keys(array_keys($this->fAttributesMap), '');

        foreach ($deletedEntities as $deletedEntity) {
            $counter++;
            $toFill = array();

            $this->found = true;
            $this->deleted++;

            $toFill['is_customer'] = $deletedEntity->getIsCustomer();
            $toFill['entity_id'] = $deletedEntity->getEntityId();
            $toFill = array_merge($toFill, $prefilled);
            $toFill['email'] = $deletedEntity->getEmail();

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

    private function _setAddressesAttributeKeys(array &$toFill, Mage_Core_Model_Abstract $data) {
        $this->_setAddressAttributeKeys($toFill, $data, "shipping");
        $this->_setAddressAttributeKeys($toFill, $data, "billing");
    }

    private function _mustExportAddress($addressType) {
        return $this->getTask()->getConfigFlag("contactlab_subscribers/global/export_" . $addressType . "_address");
    }

    private function _setAddressAttributeKeys(array &$toFill, Mage_Core_Model_Abstract $data, $addressType) {
        if (!$this->_mustExportAddress($addressType)) {
            return;
        }
        $entityTypes = array('int', 'varchar', 'text', 'decimal', 'datetime');
        foreach ($entityTypes as $type) {
            if (!array_key_exists($addressType . '_' . $type . '_value', $data->getData())) {
                return;
            }
            $values = explode($this->limiter, $data->getData($addressType . '_' . $type . '_value'));
            foreach ($values as $index => $key) {
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

    private function _setAttributeKeys(array &$toFill, Mage_Core_Model_Abstract $data) {
        $entityTypes = array('int', 'varchar', 'text', 'decimal', 'datetime');
        foreach ($entityTypes as $type) {
            if (!array_key_exists($type . '_value', $data->getData())) {
                continue;
            }
            $values = explode($this->limiter, $data->getData($type . '_value'));
            foreach ($values as $index => $key) {
                $pos = strpos($key, '_');
                if ($pos === false) {
                    continue;
                }
                $k = substr($key, 0, $pos);
                $v = substr($key, $pos + 1);
                if (!array_key_exists($k, $this->customerAttributes)) {
                    continue;
                }
                $toFill[$this->fAttributesMap[$this->customerAttributes[$k]]] = 
                        $this->helper->decode($this->customerModel, 'customer', $this->customerAttributes[$k], $v);
            }
        }
    }

    /** Add store, group and website attributes. */
    private function _fillStoreAttributes(array &$toFill, Mage_Core_Model_Abstract $item) {
        $store = $this->stores[$item->getStoreId()];
        foreach (array('store_id', 'store_name', 'website_id', 'website_name',
            'group_id', 'group_name', 'lang') as $k) {
            $toFill[$k] = $store[$k];
        }
    }

    /** Add stats to collection. */
    private function _addStatsToSelect(Varien_Data_Collection_Db $collection) {
        $collection->getSelect()->joinLeft(
                    array('stats' => $this->r->getTableName('contactlab_subscribers/stats')),
                            'stats.customer_id = e.entity_id', $this->statsAttributesMap);
    }

    /** Add customer export to collection. */
    private function _addCustomerExportCollection(Varien_Data_Collection_Db $collection) {
        $collection->getSelect()->joinLeft(
                    array('newsletter_subscriber' => $this->r->getTableName('newsletter/subscriber')),
                            'newsletter_subscriber.customer_id = e.entity_id',
                            array('subscriber_status' => 'subscriber_status'));
        $collection->getSelect()->joinLeft(
                    array('uk' => $this->r->getTableName('contactlab_subscribers/uk')),
                            'uk.customer_id = e.entity_id',
                            array('uk' => 'entity_id', 'is_exported' => 'is_exported'));
    }

    /** Add newsletter subscriber export to collection. */
    private function _addNewsletterSubscriberExportCollection(Varien_Data_Collection_Db $collection) {
        $collection->getSelect()->joinLeft(
                    array('uk' => $this->r->getTableName('contactlab_subscribers/uk')),
                            'uk.subscriber_id = main_table.subscriber_id',
                            array('uk' => 'entity_id', 'is_exported' => 'is_exported'));
    }

    // Add export policy to sql statement.
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

        $first = true;
        foreach ($fields as $table => $field) {
            if (!is_array($field)) {
                $field = array($field);
            }/*
            if (!$first) {
                $clause .= ' and ';
            }*/
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
            $first = false;
        }
        $collection->getSelect()->where($clause);
    }

    // Add export policy to sql statement.
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
        //$lastExport = Mage::getModel("core/date")->gmtDate(null, $lastExport);

        $w .= ' and (';
        $first = true;
        foreach ($fields as $table => $field) {
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


    private function _addAddressesToSelect(Varien_Data_Collection_Db $collection) {
        $this->_addAddressToSelect($collection, "shipping");
        $this->_addAddressToSelect($collection, "billing");
    }

    private function _addAddressToSelect(Varien_Data_Collection_Db $collection, $type) {
        if (!$this->getTask()->getConfigFlag("contactlab_subscribers/global/export_" . $type . "_address")) {
            return;
        }
        $collection->getSelect()->joinLeft(
                    array($type . '_addr' => $this->r->getTableName('customer_entity_int')),
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
                    $alias => $this->r->getTableName('customer_address_entity_' . $backendType)),
                        $w, array($type . '_' . $backendType . '_value' => 'GROUP_CONCAT(DISTINCT CONCAT('
                    . $alias . '.attribute_id, "_", '.$alias.'.value) SEPARATOR "' . $this->limiter . '")'));
        }
    }

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

    private function _loadStores() {
        $rv = array();
        $websiteTable = $this->r->getTableName('core/website');
        $storeGroupTable = $this->r->getTableName('core/store_group');
        $stores = Mage::getModel('core/store')
                ->getCollection()->setLoadDefault(true);
        $stores->addFieldToSelect('*')->getSelect()
            ->join(array('core_website' => $websiteTable),
                'main_table.website_id = ' . $websiteTable . '.website_id',
                array('website_name' => $websiteTable . '.name'))
            ->join(array('core_store_group' => $storeGroupTable),
                'main_table.group_id = ' . $storeGroupTable . '.group_id',
                array('group_name' => $storeGroupTable . '.name'));
        foreach ($stores as $store) {
            $rv[$store->getStoreId()] = array(
                'store_id' => $store->getStoreId(),
                'website_id' => $store->getWebsiteId(),
                'group_id' => $store->getGroupId(),
                'store_name' => $store->getName(),
                'website_name' => $store->getWebsiteName(),
                'group_name' => $store->getGroupName(),
                'lang' => $store->getConfig("general/locale/code"),
            );
        }
        return $rv;
    }

    /** Do I have to export all contacts? Or only subscribed ones? */
    private function _mustExportNotSubscribed() {
        return $this->getTask()->getConfigFlag("contactlab_subscribers/global/export_not_subscribed");
    }

    /** Do I have to reset export dates before export? */
    private function _mustResetExportDates() {
        return $this->getTask()->getConfig("contactlab_subscribers/global/reset_export_dates_before_next_export");
    }

    private function _resetMustResetExportDatesFlag() {
        Mage::helper("contactlab_commons")->logDebug("Reset reset_export_dates_before_next_export flag");
        Mage::getConfig()
            ->saveConfig('contactlab_subscribers/global/reset_export_dates_before_next_export', '0');
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    private function _resetExportDates() {
        $this->_query(sprintf("update %s set is_exported = 0;", $this->r->getTableName('contactlab_subscribers/uk')));
    }

    /** Run custom query. */
    private function _query($sql) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query($sql);
        Mage::helper("contactlab_commons")->logDebug($sql);
    }


    /** Get subscription flag name. */
    public function getSubscribedFlagName() {
        return $this->getTask()->getConfig("contactlab_subscribers/global/subscribed_flag_name");
    }

    /**
     * Manage if include CLS tag and his value.
     * Fill the persistence table.
     */
    private function _manageCustomerClsFlag(array &$toFill, $item) {
        // FIXME No, nel caso di export globale, deve mettere sempre 0!!
        //  1 = Subscribed
        //  0 = Not subscribed
        // -1 = Unsubscribed
        if ($this->_manageClsFlag($toFill, $item)) {
            if ($this->ukQuery != '') {
                $this->ukQuery .= ', ';
            }
            $this->ukQuery .= $item->getUk();
        }
    }

    /**
     * Manage if include CLS tag and his value.
     * Fill the persistence table.
     */
    private function _manageNewsletterClsFlag(array &$toFill, $item) {
        if ($this->_manageClsFlag($toFill, $item, true)) {
            if ($this->ukQuery != '') {
                $this->ukQuery .= ', ';
            }
            $this->ukQuery .= $item->getUk();
        }
    }

    /**
     * Manage if include CLS tag and his value.
     * Fill the persistence table.
     */
    private function _manageClsFlag(array &$toFill, $item, $force = false) {
        // If processing record has (first) ExportDate, return.
        if ($item->hasIsExported() && $item->getIsExported() == 1 && !$force) {
            return false;
        }
        $toFill[$this->getSubscribedFlagName()]
            = $item->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
                ? 1 : 0;
        return true;
    }

    /** Enabled? */
    protected function isEnabled() {
        return Mage::helper("contactlab_subscribers")->isEnabled($this->getTask());
    }

    /**
     * Get file name.
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
}
