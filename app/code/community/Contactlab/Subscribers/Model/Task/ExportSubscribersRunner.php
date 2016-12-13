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
        if ($task->getConfigFlag("contactlab_commons/soap/enable")) {
            $this->_checkSubscriberDataExchangeStatus();
        }
        if (!Mage::helper('contactlab_subscribers/checks')->checkAvailableEssentialChecks()) {
            throw Mage::helper('contactlab_subscribers/checks')->getLastCheckException($task);
        }
        if ($task->getConfig("contactlab_subscribers/global/check_uk_before_export") == '1') {
            if (!$this->_checkUk()) {
                throw new Exception("UK Table inconsistent, please fix it with the \"Update unique Keys\" button in the task page!");
            }
        } else if ($task->getConfig("contactlab_subscribers/global/check_uk_before_export") == '2') {
            if (!$this->_checkUk(true)) {
                $this->_repairUk(true);
                if (!$this->_checkUk()) {
                    throw new Exception("UK Table inconsistent, please fix it with the \"Update unique Keys\" button in the task page!");
                }
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

    /**
     * Check UK table.
     * @param boolean $skipMessages
     * @return boolean
     */
    private function _checkUk($skipMessages = false) {
        /* @var $helper Contactlab_Subscribers_Helper_Uk */
        $helper = Mage::helper('contactlab_subscribers/uk');
        $this->getTask()->setSuppressSuccessUk(true);
        $this->getTask()->setSkipMessages($skipMessages);
        return $helper->updateAll(false, $this->getTask());
        return true;
    }

    /**
     * Repair UK table.
     * @param boolean $skipMessages
     * @return boolean
     */
    private function _repairUk($skipMessages = false)
    {
        /* @var $helper Contactlab_Subscribers_Helper_Uk */
        $helper = Mage::helper('contactlab_subscribers/uk');
        $this->getTask()->setSuppressSuccessUk(true);
        $this->getTask()->setSkipMessages($skipMessages);
        return $helper->updateAll(true, $this->getTask());
        return true;
    }
}
