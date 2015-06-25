<?php

/**
 * Task events model.
 */
class Contactlab_Commons_Model_Task_Event extends Mage_Core_Model_Abstract {
    /** Construct. */
    public function _construct() {
        $this->_init("contactlab_commons/task_event");
    }

    /** Get task status. */
    public function getStatus() {
        return $this->getTaskStatus();
    }

}
