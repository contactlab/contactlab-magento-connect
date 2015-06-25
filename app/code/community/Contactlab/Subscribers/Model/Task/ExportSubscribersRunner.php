<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_ExportSubscribersRunner extends Contactlab_Commons_Model_Task_Abstract {

    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        if ($this->getTask()->getConfigFlag("contactlab_subscribers/global/soap_call_after_export")) {
            $this->_checkSubscriberDataExchangeStatus();
        }
    	$this->setExporter(Mage::getModel("contactlab_subscribers/exporter_subscribers")
			->setTask($this->getTask()));
        return $this->getExporter()->export($this);
    }

    /**
     * Called after the run.
     */
    protected function _afterRun() {
        $this->getExporter()->afterExport();
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Export subscribers";
    }

    /**
     * Mermory limit.
     * @return Memory limit or void if not to be modified.
     */
    public function getMemoryLimit() {
        return Mage::getStoreConfig("contactlab_subscribers/global/memory_limit");
    }
}
