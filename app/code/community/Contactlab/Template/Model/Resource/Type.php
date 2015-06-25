<?php

/** Template type resource. */
class Contactlab_Template_Model_Resource_Type extends Mage_Core_Model_Mysql4_Abstract {

    /** Construct. */
    public function _construct() {
        $this->_init("contactlab_template/type", "entity_id");
    }
}
