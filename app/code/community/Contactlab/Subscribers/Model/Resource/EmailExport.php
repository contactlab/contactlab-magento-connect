<?php

/**
 * Customer export resource.
 */
class Contactlab_Subscribers_Model_Resource_CustomerExport extends Mage_Core_Model_Mysql4_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/customer_export", "entity_id");
    }
}
