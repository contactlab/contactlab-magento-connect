<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_ImportSubscribersRunner extends Contactlab_Commons_Model_Task_Abstract {

    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        if ($this->getTask()->getConfigFlag("contactlab_commons/soap/enable")) {
            $this->_checkSubscriberDataExchangeStatus();
        }
        return Mage::getModel("contactlab_subscribers/importer_subscribers")
	        ->setTask($this->getTask())
	        ->import($this);
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Import subscribers";
    }

    /**
     * Mermory limit.
     * @return Memory limit or void if not to be modified.
     */
    public function getMemoryLimit() {
        return Mage::getStoreConfig("contactlab_subscribers/global/memory_limit");
    }
}
