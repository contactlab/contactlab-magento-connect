<?php

/**
 * Cron model.
 */
class Contactlab_Commons_Model_Cron {

    /**
     * Consume the queue.
     */
    public function consumeQueue() {
        Mage::helper("contactlab_commons/tasks")->consume();
    }

    /**
     * Add test queue.
     */
    public function addTestQueue() {
        Mage::getModel("contactlab_commons/task")->setMaxRetries(10)
                ->setTaskCode("TestTaskCode")
                ->setModelName('contactlab_commons/task_testRunner')
                ->setDescription('Test runner task')->save();
    }

    /**
     * Removes old closed tasks from the queue.
     */
    public function clearQueue() {
        Mage::helper("contactlab_commons/tasks")->clearQueue();
    }

    /**
     * Removes old closed tasks from the queue.
     */
    public function checkErrors() {
    	Mage::helper("contactlab_commons/tasks")->sendEmail();
    }

}
