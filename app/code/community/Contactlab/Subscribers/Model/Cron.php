<?php

/**
 * Cron model.
 */
class Contactlab_Subscribers_Model_Cron extends Varien_Object {

    /**
     * Add exporter task to queue.
     */
    public function addExportSubscribersQueue($storeId = 0) {
        if (!$this->isModuleEnabled()) {
            return;
        }
        $this->logCronCall("addExportSubscribersQueue", $storeId);
        if (is_object($storeId)) {
            $storeId = $this->_getStoreId($storeId);
        }
        return Mage::getModel("contactlab_commons/task")
                ->setStoreId($storeId)
                ->setTaskCode("ExportSubscribersTask")
                ->setModelName('contactlab_subscribers/task_exportSubscribersRunner')
                ->setDescription('Export Subscribers')
                ->save();
    }

    /**
     * Add exporter task to queue.
     */
    public function addCalcStatsQueue() {
        if (!$this->isModuleEnabled()) {
            return;
        }
        $this->logCronCall("addCalcStatsQueue");
        Mage::helper("contactlab_subscribers")->addCalcStatsQueue();
    }

    /**
     * Add exporter task to queue.
     */
    public function addStartSubscriberDataExchangeRunnerQueue($storeId = 0) {
        if (!$this->isModuleEnabled()) {
            return;
        }
        if (is_object($storeId)) {
            $storeId = $this->_getStoreId($storeId);
        }
        $this->logCronCall("addStartSubscriberDataExchangeRunnerQueue", $storeId);
        return Mage::getModel("contactlab_commons/task")
                ->setStoreId($storeId)
                ->setTaskCode("StartSubscriberDataExchangeRunner")
                ->setModelName('contactlab_subscribers/task_startSubscriberDataExchangeRunner')
                ->setDescription('Start Subscriber Data ExchangeRunner')
                ->save();
    }

    /**
     * Add importer task to queue.
     */
    public function addImportSubscribersQueue($storeId = 0) {
        if (!$this->isModuleEnabled()) {
            return;
        }
        if (is_object($storeId)) {
            $storeId = $this->_getStoreId($storeId);
        }
        $this->logCronCall("addImportSubscribersQueue", $storeId);
        return Mage::getModel("contactlab_commons/task")
                ->setStoreId($storeId)
                ->setTaskCode("ImportSubscribersTask")
                ->setModelName('contactlab_subscribers/task_importSubscribersRunner')
                ->setDescription('Import Subscribers')
                ->save();
    }

    /**
     * Get store id from xml config node.
     *
     * @param XML Object $schedule
     * @return String
     */
    private function _getStoreId($schedule) {
        $jobsRoot = Mage::getConfig()->getNode('crontab/jobs');
        $jobConfig = $jobsRoot->{$schedule->getJobCode()};
        $storeNode = (string) $jobConfig->store;
        return $storeNode;
    }

    /**
     * Is module enabled?
     * @return boolean
     */
    protected function isModuleEnabled() {
        return Mage::getStoreConfigFlag('contactlab_subscribers/global/enabled');
    }

    /**
     * Log function call.
     * @param String $functionName
     * @param String $storeId
     */
    public function logCronCall($functionName, $storeId, $storeId = false)
    {
        Mage::helper('contactlab_commons')
            ->logCronCall(
                "Contactlab_Subscribers_Model_Cron::$functionName", $storeId
            );
    }
}
