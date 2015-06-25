<?php

class Contactlab_Template_Model_Task_ProcessNewsletterQueueRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        $data = $this->getTask()->getTaskData();
        $data = unserialize($data);
        $queueId = $data['queue_id'];
        $storeId = $data['store_id'];
        $queue = Mage::getModel('newsletter/queue')->load($queueId);
        $queue->setQueueStartAt(Mage::getSingleton('core/date')->gmtDate())
                ->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING)
                ->save();
        $queue->setStoreId($storeId);
        $queue->setTask($this->getTask());

        return $queue->sendPerSubscriber();
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Process newsletter queue";
    }
}
