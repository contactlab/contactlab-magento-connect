<?php

class Contactlab_Template_Model_Task_CheckNewsletterQueueRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        $data = $this->getTask()->getTaskData();
        $data = unserialize($data);

        $parentTask = Mage::getModel('contactlab_commons/task')
            ->load($data['task_id']);

        return Mage::helper('contactlab_template')
            ->checkNewsletterQueueReport($this->getTask(),
                $parentTask,
                $data['xml_filename'],
                $data['store_id'],
                $data['queue_id']);
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Check newsletter queue";
    }
}
