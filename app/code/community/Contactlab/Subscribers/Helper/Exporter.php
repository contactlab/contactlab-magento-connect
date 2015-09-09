<?php

/**
 * Exporter helper.
 */
class Contactlab_Subscribers_Helper_Exporter extends Mage_Core_Helper_Abstract {
    private $attributeSource = array();
    private $noSourceAttributes = array();

    /** Decode id attributes.
     * @param Mage_Core_Model_Abstract $model
     * @param $name
     * @param $attributeName
     * @param $value
     * @return string
     */
    public function decode(Mage_Core_Model_Abstract $model, $name, $attributeName, $value) {
        $ak = $name . '_' . $attributeName;
        if (array_key_exists($ak, $this->noSourceAttributes)) {
            return $value;
        }
        $resource = $model->getResource();
        if (!array_key_exists($ak, $this->attributeSource)) {
            $attribute = $resource->getAttribute($attributeName);
            if ($attribute->getSourceModel()) {
                $this->attributeSource[$ak] = $attribute->getSource();
            } else {
                $this->noSourceAttributes[$ak] = $ak;
                return $value;
            }
        }
        if (!$value) {
            return '';
        }
        return $this->attributeSource[$ak]->getOptionText($value);
    }

    /**
     * Attributes map for customer.
     * @param Contactlab_Commons_Model_Task $task
     * @return array
     */
    public function getAttributesMap(Contactlab_Commons_Model_Task $task) {
        return array_merge(array(
            'prefix' => 'prefix',
            'firstname' => 'firstname',
            'middlename' => 'middlename',
            'lastname' => 'lastname',
            'suffix' => 'suffix',
            'dob' => 'dob',
            'gender' => 'gender',
            'email' => 'email',
            'created_at' => 'created_at',
            /**
             * Adding new fields from extended newsletter subscription form
             */
            'privacy' => 'privacy',
            'mobilephone' => 'mobilephone',
            'notes' => 'notes',
            'custom_1' => 'custom_1',
            'custom_2' => 'custom_2'
        ), $this->_getCustomAttributesMap($task));
    }

    /** Attributes map for addresses. */
    public function getAddressesAttributesMap() {
        return array(
            'country_id' => 'country_id',
            'region_id' => 'region_id',
            'region' => 'region',
            'postcode' => 'postcode',
            'city' => 'city',
            'street' => 'street',
            'telephone' => 'telephone',
            'fax' => 'fax',
            'company' => 'company',
        );
    }

    /**
     * Attributes map for stats.
     * @param Contactlab_Commons_Model_Task $task
     * @return array
     */
    private function _getCustomAttributesMap(Contactlab_Commons_Model_Task $task) {
        $rv = array();
        foreach (range(1, 7) as $i) {
            if ($task->getConfigFlag("contactlab_subscribers/custom_fields/enable_field_" . $i)) {
                $rv['cstm_' . $i] = $task->getConfig("contactlab_subscribers/custom_fields/field_" . $i);
            }
        }
        return $rv;
    }

    /** Stats attributes names. */
    public function getStatsAttributesMap() {
        return array(
            'last_order_date' => 'last_order_date',
            'last_order_amount' => 'last_order_amount',
            'last_order_products' => 'last_order_products',
            'total_orders_amount' => 'total_orders_amount',
            'total_orders_products' => 'total_orders_products',
            'total_orders_count' => 'total_orders_count',
            'avg_orders_amount' => 'avg_orders_amount',
            'avg_orders_products' => 'avg_orders_products',
            'period1_amount' => 'period1_amount',
            'period1_products' => 'period1_products',
            'period1_orders' => 'period1_orders',
            'period2_amount' => 'period2_amount',
            'period2_products' => 'period2_products',
            'period2_orders' => 'period2_orders',
        );
    }

    /**
     *     Map new subscriber fields names to customer attribute names
     */
    public function getSubscribertoCustomerAttributeMap() {
        return array(
            'first_name' => 'firstname',
            'last_name' => 'lastname',
            'company' => 'billing_company',
            'gender' => 'gender',
            'dob' => 'dob',
            'privacy_accepted' => 'privacy',
            'country' => 'billing_country',
            'city' => 'billing_city',
            'address' => 'billing_street',
            'zip_code' => 'billing_postcode',
            'phone' => 'billing_telephone',
            'cell_phone' => 'mobilephone',
            'notes' => 'notes',
            'custom_1' => 'custom_1',
            'custom_2' => 'custom_2'
        );
    
    }

    /**
     * Entity type id from name.
     * @param $name
     * @return null|string
     */
    public function getEntityTypeId($name) {
        $types = Mage::getModel("eav/entity_type")->getCollection();
        foreach ($types as $type) {
            if ($type->getEntityTypeCode() === $name) {
                return $type->getEntityTypeId();
            }
        }
        return null;
    }

    /**
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributes
     * @return array
     */
    public function fillBackendTypesFromArray(Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributes) {
        $types = array();
        foreach ($attributes as $row) {
            $backendType = $row->getData('backend_type');
            if (!isset($types[$backendType])) {
                $types[$backendType] = array();
            }
            $types[$backendType][] = $row->getAttributeId(); 
        }
        return $types;
    }

    /**
     * Attributes collection for entity type.
     * @param $entityType
     * @param array $attributes
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributesForEntityType($entityType, array $attributes) {
        return Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter('backend_type', array('neq' => 'static'))
            ->addFieldToFilter('attribute_code', array('in' => $attributes))
            ->addFieldToFilter('entity_type_id', $this->getEntityTypeId($entityType));
    }

    /**
     * Attributes collection for entity type.
     * @param $entityType
     * @return array
     */
    public function getAttributesCodesForEntityType($entityType) {
        $rv = array();
        foreach (Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter('entity_type_id', $this->getEntityTypeId($entityType)) as $item) {
            $rv[$item->getAttributeId()] = $item->getAttributeCode();
        }
        return $rv;
    }

    /**
     * Attributes collection for entity type.
     * @param $entityType
     * @param $attributeCode
     * @return bool|int|null
     */
    public function getAttributeId($entityType, $attributeCode) {
        foreach (Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter('attribute_code', $attributeCode)
            ->addFieldToFilter('entity_type_id', $this->getEntityTypeId($entityType)) as $r) {
            return $r->getAttributeId();
        }
        return false;
    }

    /** Manage export policy data after export success. */
    public function saveLastExportDatetime(Contactlab_Commons_Model_Task $task) {
        // FIXME diff by store?
        $lastExport = Mage::getModel("core/date")->gmtDate();
        Mage::getConfig()
                ->saveConfig('contactlab_subscribers/global/last_export',
                    $lastExport);
        if ($task->getConfig("contactlab_subscribers/global/export_policy") == '1') {
            Mage::getConfig()
                    ->saveConfig('contactlab_subscribers/global/export_policy', '2');
        }
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * Calls ContactLab SOAP StartSubscriberDataExchange method.
     * @param Contactlab_Commons_Model_Task $task
     */
    public function soapCallAfterExport(Contactlab_Commons_Model_Task $task) {
        if ($task->getConfigFlag("contactlab_commons/soap/enable")
                && $task->getConfigFlag("contactlab_subscribers/global/soap_call_after_export")) {
            Mage::getModel('contactlab_subscribers/cron')->addStartSubscriberDataExchangeRunnerQueue($task->getStoreId());
            Mage::getModel('contactlab_commons/cron')->consumeQueue();
        }
    }
}
