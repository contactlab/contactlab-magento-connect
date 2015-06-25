<?php

/**
 * Task event collection.
 */
class Contactlab_Commons_Model_Resource_Task_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_commons/task_event");
    }

}
