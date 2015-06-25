<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_CalcStatsRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        return Mage::helper("contactlab_subscribers")
                ->calcStatistcs($this->getTask());
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Calculate statistics";
    }

    /**
     * Mermory limit.
     * @return Memory limit or void if not to be modified.
     */
    public function getMemoryLimit() {
        return Mage::getStoreConfig("contactlab_subscribers/global/memory_limit");
    }
}
