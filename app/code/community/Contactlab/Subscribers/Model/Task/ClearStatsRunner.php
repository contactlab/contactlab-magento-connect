<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_ClearStatsRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        return Mage::getResourceModel("contactlab_subscribers/stats")->clear();
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Clear statistics";
    }
}
