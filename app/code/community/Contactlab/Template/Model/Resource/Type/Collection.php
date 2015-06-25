<?php

/** Template type collection. */
class Contactlab_Template_Model_Resource_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /** Construct. */
    public function _construct() {
        $this->_init("contactlab_template/type");
    }

}
