<?php

/**
 * Cron model.
 */
class Contactlab_Commons_Model_Cron {

    /**
     * Consume the queue.
     */
    public function consumeQueue() {
        $this->logCronCall('consumeQueue');
        Mage::helper("contactlab_commons/tasks")->consume();
    }

    /**
     * Add test queue.
     */
    public function addTestQueue() {
        $this->logCronCall('addTestQueue');
        Mage::getModel("contactlab_commons/task")->setMaxRetries(10)
                ->setTaskCode("TestTaskCode")
                ->setModelName('contactlab_commons/task_testRunner')
                ->setDescription('Test runner task')->save();
    }

    /**
     * Removes old closed tasks from the queue.
     */
    public function clearQueue() {
        $this->logCronCall('clearQueue');
        Mage::helper("contactlab_commons/tasks")->clearQueue();
    }

    /**
     * Removes old closed tasks from the queue.
     */
    public function checkErrors() {
        $this->logCronCall('checkErrors');
    	Mage::helper("contactlab_commons/tasks")->sendEmail();
    }

    /**
     * Log function call.
     * @param String $functionName
     * @param String $storeId
     */
    public function logCronCall($functionName, $storeId = false)
    {
        Mage::helper('contactlab_commons')
            ->logCronCall(
                "Contactlab_Commons_Model_Cron::$functionName", $storeId
            );
    }
}
