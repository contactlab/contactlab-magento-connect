<?php

/**
 * Customer export collection.
 */
class Contactlab_Subscribers_Model_Resource_CustomerExport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/customer_export");
    }
}
