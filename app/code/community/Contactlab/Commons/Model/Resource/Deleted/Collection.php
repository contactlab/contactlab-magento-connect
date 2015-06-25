<?php

/**
 * Deleted customers and subscribers collection.
 */
class Contactlab_Commons_Model_Resource_Deleted_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_commons/deleted");
    }

}
