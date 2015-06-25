<?php

/**
 * Stats collection.
 */
class Contactlab_Subscribers_Model_Resource_Uk_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/uk");
    }
}
