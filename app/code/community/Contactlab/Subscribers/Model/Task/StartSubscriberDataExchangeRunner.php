<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_StartSubscriberDataExchangeRunner extends Contactlab_Commons_Model_Task_Abstract {

    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
    	$this->_checkSubscriberDataExchangeStatus();
        return Mage::getModel("contactlab_subscribers/soap_startSubscriberDataExchangeCall")
	        ->singleCall($this->getTask());
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Start Subscriber Data Exchange";
    }

    /**
     * Mermory limit.
     * @return Memory limit or void if not to be modified.
     */
    public function getMemoryLimit() {
        return Mage::getStoreConfig("contactlab_subscribers/global/memory_limit");
    }
}
