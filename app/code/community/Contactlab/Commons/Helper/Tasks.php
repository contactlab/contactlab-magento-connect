<?php

/**
 * Tasks management helper.
 */
class Contactlab_Commons_Helper_Tasks extends Mage_Core_Helper_Abstract {
    private $errorTasks = array();

    /**
     * Consume the queue.
     */
    public function consume() {
        $h = Mage::helper("contactlab_commons");
        $tasks = Mage::getModel("contactlab_commons/task")
                ->getCollection()->loadReadyToConsumeTasks();
        $first = true;
        foreach ($tasks as $task) {
            if ($first) {
                $h->logInfo("Consuming task queue");
            }
            $h->logDebug(sprintf("Found task \"%s\" with status \"%s\". Retry %d/%d",
                    $task->getDescription(), $task->getStatus(),
                    $task->getNumberOfRetries(), $task->getMaxRetries()));
            if (!$task->isEnabled()) {
                return;
            }
            $task->runTask();
            $first = false;
        }
        if ($first) {
		    if (Mage::helper('contactlab_commons')->isDebug()) {
                // $h->logInfo("Empty queue");
            }
        } else {
            $h->logInfo("Done");
        }
    }

    /**
     * Clear the queue.
     */
    public function clearQueue() {
        $tasks = Mage::getModel("contactlab_commons/task")
                ->getCollection()->loadOldTasks();
		$this->_clearQueue($tasks);
        $tasks = Mage::getModel("contactlab_commons/task")
                ->getCollection()->loadOldFailedTasks();
		$this->_clearQueue($tasks);
	}

    /**
     * Clear the queue.
     */
    private function _clearQueue($tasks) {
        $h = Mage::helper("contactlab_commons");
        $first = true;
        foreach ($tasks as $task) {
            $h->logInfo(sprintf("Clear old task \"%s\"", $task->getDescription()));
            $task->delete();
            $first = false;
        }
        if ($first) {
            $h->logInfo("No old task to clear");
        } else {
            $h->logInfo("Queue cleaned");
        }
    }

    /**
     * Send notification email.
     */
    public function sendEmail() {
        if (!Mage::getStoreConfigFlag('contactlab_commons/error_email/enable')) {
            return;
        }
		$this->setErrorTasks($this->loadErrorTasks());
		if ($this->getErrorTasks()->count() === 0) {
			return;
		}
        $translate = Mage::getSingleton('core/translate');

        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template');

        $templateId = Mage::getStoreConfig('contactlab_commons/error_email/send_email_template',
        	Mage::app()->getStore()->getId());
        $senderEmail = Mage::getStoreConfig('contactlab_commons/error_email/email_sender');
        $senderName = Mage::getStoreConfig('contactlab_commons/error_email/email_sender_name');
        $recipients = explode(';', Mage::getStoreConfig('contactlab_commons/error_email/recipients'));
        
        foreach ($recipients as $recipient) {
            if (empty($recipient)) {
                continue;
            }
            $mailTemplate->sendTransactional(
                    $templateId,
                    array('name' => $senderName,
                            'email' => $senderEmail),
                    $recipient,
                    Mage::helper('contactlab_commons')->__('Tasks error notification'),
                    array(),
                    Mage::app()->getStore()->getId()
            );
        }

        $translate->setTranslateInline(true);

        $this->updateErrorTaskArray();

        return $this;
    }

    public function updateErrorTaskArray() {
        foreach ($this->getErrorTasks() as $task) {
        	Mage::getModel('contactlab_commons/task_event')->load($task->getTaskEventId())
        		->setEmailSent(1)->save();
        }
    }
	/** Get events with alert flg. */
    public function setErrorTasks($tasks) {
    	$this->errorTasks = $tasks;
	}

	/** Get events with alert flg. */
    public function getErrorTasks() {
    	return $this->errorTasks;
	}

	/** Get events with alert flg. */
    private function loadErrorTasks() {
        $tasks = Mage::getModel('contactlab_commons/task')->getCollection();
        $tasks->addFieldToSelect('task_code')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('planned_at')
            ->addFieldToSelect('description')
            ->addFieldToSelect('task_data')
            ->addFieldToSelect('number_of_retries')
            ->addFieldToSelect('max_retries')
            ->addFieldToSelect('retries_interval')
            ->addFieldToSelect('status')
            ->getSelect()
            ->join(array('task_event' => Mage::getSingleton('core/resource')->getTableName('contactlab_commons_task_event_entity')),
                'main_table.task_id = task_event.task_id AND task_event.email_sent = 0 AND task_event.send_alert = 1',
                array('task_event_id' => 'task_event.task_event_id',
                      'created_at_' => 'task_event.created_at', 
                      'task_status' => 'task_event.task_status', 
                      'description_' => 'task_event.description'));

        return $tasks;
    }
}
