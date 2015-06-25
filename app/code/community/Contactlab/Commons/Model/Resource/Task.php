<?php

/**
 * Task resource.
 */
class Contactlab_Commons_Model_Resource_Task extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_commons/task", "task_id");
    }
}
