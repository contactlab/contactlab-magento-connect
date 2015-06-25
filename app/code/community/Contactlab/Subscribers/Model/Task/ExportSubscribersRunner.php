<?php

/**
 * Task runner for exporting.
 */
class Contactlab_Subscribers_Model_Task_ExportSubscribersRunner extends Contactlab_Commons_Model_Task_Abstract {

    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        /* @var $task Contactlab_Commons_Model_Task */
        $task = $this->getTask();
        if ($task->getConfigFlag("contactlab_subscribers/global/soap_call_after_export")) {
            $this->_checkSubscriberDataExchangeStatus();
        }
        if ($task->getConfigFlag("contactlab_subscribers/global/check_uk_before_export")) {
            if (!$this->_checkUk()) {
                throw new Exception("UK Table inconsistent, please fix it with the \"Update unique Keys\" button in the task page!");
            }
        }
    	$this->setExporter(Mage::getModel("contactlab_subscribers/exporter_subscribers")
			->setTask($this->getTask()));
        return $this->getExporter()->export($this);
    }

    /**
     * Called after the run.
     */
    protected function _afterRun() {
        if ($this->hasExporter()) {
            $this->getExporter()->afterExport();
        }
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

    public function _checkUk() {
        /* @var $helper Contactlab_Subscribers_Helper_Uk */
        $helper = Mage::helper('contactlab_subscribers/uk');
        $this->getTask()->setSuppressSuccessUk(true);
        return $helper->updateAll(false, $this->getTask());
    }

}
