<?php

/**
 * Cron model.
 */
class Contactlab_Subscribers_Model_Cron extends Varien_Object {

    /**
     * Add exporter task to queue.
     */
    public function addExportSubscribersQueue($storeId = 0) {
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
        Mage::helper("contactlab_subscribers")->addCalcStatsQueue();
    }

    /**
     * Add exporter task to queue.
     */
    public function addStartSubscriberDataExchangeRunnerQueue($storeId = 0) {
        if (is_object($storeId)) {
            $storeId = $this->_getStoreId($storeId);
        }
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
        if (is_object($storeId)) {
            $storeId = $this->_getStoreId($storeId);
        }
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
}
