<?php

/**
 * Contactlab template model task observer.
 * Remove queue rows if present.
 */
class Contactlab_Template_Model_Task_Observer {
    /**
     * Task delete.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function taskDelete(Varien_Event_Observer $observer) {
        $task = $observer->getTask();
        if ($this->_hasNewsletterQueue($task)) {
            $task->setPreventDelete(true);
        } else {
            $this->_deleteNewsletterQueue($task);
        }
    }

    /**
     * Load old tasks, include hidden orphan rows.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function loadOldTasks(Varien_Event_Observer $observer) {
        $collection = $observer->getCollection();
        $collection->getSelect()->orWhere("status = ? and main_table.task_id not in (select task_id from newsletter_queue)",
            Contactlab_Commons_Model_Task::STATUS_HIDDEN);
    }

    /**
     * Does the task has newsletter queue that cannot be deleted?
     *
     * @param Contactlab_Commons_Model_Task $task
     * @return void
     */
    private function _hasNewsletterQueue(Contactlab_Commons_Model_Task $task) {
        $collection = Mage::getResourceModel('newsletter/queue_collection')
                ->addFieldToFilter('task_id', $task->getTaskId());
        $collection->getSelect()
                ->where('main_table.queue_status in (?)', array(
                        Mage_Newsletter_Model_Queue::STATUS_SENDING,
                        Mage_Newsletter_Model_Queue::STATUS_SENT,
                        Mage_Newsletter_Model_Queue::STATUS_PAUSE));
        return $collection->count() > 0;
    }

    /**
     * Does the task has newsletter queue that cannot be deleted?
     *
     * @param Contactlab_Commons_Model_Task $task
     * @return void
     */
    private function _deleteNewsletterQueue(Contactlab_Commons_Model_Task $task) {
        $resource = Mage::getResourceModel('newsletter/queue');
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

        $collection = Mage::getResourceModel('newsletter/queue_collection')
                ->addFieldToFilter('task_id', $task->getTaskId());
        $collection->getSelect()
                ->where('main_table.queue_status not in (?)', array(
                        Mage_Newsletter_Model_Queue::STATUS_SENDING,
                        Mage_Newsletter_Model_Queue::STATUS_PAUSE,
                        Mage_Newsletter_Model_Queue::STATUS_SENT));

        $adapter->query(Mage::helper('contactlab_commons')
                ->deleteFromSelect($adapter, $collection->getSelect(),
                'main_table'));
    }
}
