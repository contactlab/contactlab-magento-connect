<?php

/**
 * Task runner for update uk.
 */
class Contactlab_Subscribers_Model_Task_UpdateUkRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        /* @var $helper Contactlab_Subscribers_Helper_Uk */
        $helper = Mage::helper('contactlab_subscribers/uk');
        $helper->updateAll(true, $this->getTask());
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Update UK table";
    }
}
