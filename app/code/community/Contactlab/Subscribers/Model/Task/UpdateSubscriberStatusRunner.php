<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_UpdateSubscriberStatusRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
    	$data = json_decode($this->getTask()->getTaskData());
		return Mage::helper("contactlab_subscribers")->updateSubscriberStatus(
		  $data->email, $data->entityId, $data->isSubscribed, $data->storeId, false);
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Update subscriber status";
    }

    /**
     * Mermory limit.
     * @return Memory limit or void if not to be modified.
     */
    public function getMemoryLimit() {
        return Mage::getStoreConfig("contactlab_subscribers/global/memory_limit");
    }
}
