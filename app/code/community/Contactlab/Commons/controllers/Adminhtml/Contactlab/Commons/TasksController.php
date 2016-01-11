<?php

/**
 * Tasks controller.
 */
class Contactlab_Commons_Adminhtml_Contactlab_Commons_TasksController extends Mage_Adminhtml_Controller_Action {

	/**
	 * Index.
	 */
	public function indexAction() {
		$this->_title($this->__('Tasks'));
		$this->loadLayout()->_setActiveMenu('newsletter/contactlab');
		// Mage::getModel('contactlab_commons/cron')->checkErrors();
		return $this->renderLayout();
	}

	/**
	 * Grid.
	 */
	public function gridAction() {
		return $this->loadLayout(false)->renderLayout();
	}

	/**
	 * Consume tasks queue.
	 */
	public function consumeAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'consume')) {
			$session->addError(Mage::helper('adminnotification')->__('Consume task is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		Mage::getModel('contactlab_commons/cron')->consumeQueue();
		$this->_redirect('*/*');
	}

	/**
	 * Suspend.
	 */
	public function suspendAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'suspend')) {
			$session->addError(Mage::helper('adminnotification')->__('Suspend task is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::getModel("contactlab_commons/task")->load($this->getRequest()->getParam("task_id"))->suspend()->save();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', 1, $this->__('Suspend task')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $this->__('Suspend task'), $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/**
	 * Suspend.
	 */
	public function deleteAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'delete')) {
			$session->addError(Mage::helper('adminnotification')->__('Delete task is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::getModel("contactlab_commons/task")->load($this->getRequest()->getParam("task_id"))->delete()->save();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been deleted %2$s.', 1, $this->__('Delete task')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while deleting the task %d.', $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/**
	 * Clear.
	 */
	public function clearAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'clear')) {
			$session->addError(Mage::helper('adminnotification')->__('Clear queue is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::helper("contactlab_commons/tasks")->clearQueue();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', 1, $this->__('Clear Queue')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $this->__('Clear Queue'), $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/**
	 * Unsuspend.
	 */
	public function unsuspendAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'unsuspend')) {
			$session->addError(Mage::helper('adminnotification')->__('Unsuspend task is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::getModel("contactlab_commons/task")->load($this->getRequest()->getParam("task_id"))->unsuspend()->save();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', 1, $this->__('Unsuspend task')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $this->__('Unsuspend task'), $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/**
	 * Retry.
	 */
	public function retryAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'retry')) {
			$session->addError(Mage::helper('adminnotification')->__('Retry is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::getModel("contactlab_commons/task")->load($this->getRequest()->getParam("task_id"))->reset()->save();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', 1, $this->__('Retry')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $this->__('Retry'), $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/**
	 * Cancel.
	 */
	public function cancelAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'cancel')) {
			$session->addError(Mage::helper('adminnotification')->__('Cancel task is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::getModel("contactlab_commons/task")->load($this->getRequest()->getParam("task_id"))->cancel()->save();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', 1, $this->__('Cancel task')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $this->__('Cancel task'), $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/**
	 * Run the task.
	 */
	public function runAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if (!Mage::helper('contactlab_commons')->isAllowed('tasks', 'run')) {
			$session->addError(Mage::helper('adminnotification')->__('Run task is not allowed.'));
			$this->_redirect('*/*');
			return;
		}
		try {
			Mage::getModel("contactlab_commons/task")->load($this->getRequest()->getParam("task_id"))->runTask()->save();
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', 1, $this->__('Run task')));
		} catch (Exception $e) {
			$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $this->__('Run task'), $this->getRequest()->getParam("task_id")));
		}
		$this->_redirect('*/*');
	}

	/** Get request status ajax. */
	public function getRequestStatusAction() {
		$rv = new stdClass();
        // FIXME multi store
		$r = Mage::getModel('contactlab_commons/soap_getSubscriberDataExchangeStatus')->singleCall();
        $rv->status = $r;
        if ($r === 'COMPLETED') {
    		$rv->label = $this->__("API Soap service up and running");
        } else if ($r === 'DISABLED') {
            $rv->label = "";
        } else {
    		$rv->label = sprintf($this->__("<strong>Subscriber DataExchange is in Status <em>%s</em></stong>"), $this->__($r));
        }
		$this->getResponse()->setBody(JSON_encode($rv));
	}

	/** Get tasks statuses. */
	public function getStatusAction() {
		$ids = json_decode($this->getRequest()->getPost('ids'));
		$rv = array();
		$coll = Mage::getModel("contactlab_commons/task")->getCollection()
			->addFieldToFilter('task_id', array('in' => $ids));
		foreach ($coll as $task) {
			$i = new stdClass();
			$i->html = Contactlab_Commons_Block_Adminhtml_Tasks_Renderer_Model::renderTask($task);
			$i->statusHtml = Contactlab_Commons_Block_Adminhtml_Events_Renderer_Status::renderTask($task);
			$i->actionsHtml = Contactlab_Commons_Block_Adminhtml_Tasks_Renderer_Actions::renderTask($task);
			$i->id = "task-" . $task->getTaskId();
			$i->id2 = "task-status-" . $task->getTaskId();
			$i->id3 = "task-action-" . $task->getTaskId();
			$rv[] = $i;
		}
		$this->getResponse()->setBody(JSON_encode($rv));
	}

	/**
	 * Set task status mass action
	 */
	public function setStatusAction() {
		$session = Mage::getSingleton('adminhtml/session');
		$taskIds = $this->getRequest()->getParam("task_id");
		$taskStatus = $this->getRequest()->getParam("task_status");
		$num = 0;
		foreach ($taskIds as $id) {
			switch ($taskStatus) {
				case 1:
					$type = $this->__('Cancel task');
					try {
						Mage::getModel('contactlab_commons/task')->load($id)->cancel()->save();
						++$num;
					} catch (Exception $e) {
						$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $type, $id));
					}
					break;
				case 2:
					$type = $this->__('Suspend task');
					try {
						Mage::getModel('contactlab_commons/task')->load($id)->suspend()->save();
						++$num;
					} catch (Exception $e) {
						$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $type, $id));
					}
					break;
				case 3:
					$type = $this->__('Unsuspend task');
					try {
						Mage::getModel('contactlab_commons/task')->load($id)->unsuspend()->save();
						++$num;
					} catch (Exception $e) {
						$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $type, $id));
					}
					break;
				case 4:
					$type = $this->__('Retry');
					try {
						Mage::getModel('contactlab_commons/task')->load($id)->reset()->save();
						++$num;
					} catch (Exception $e) {
						$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $type, $id));
					}
					break;
				case 5:
					$type = $this->__('Run task');
					try {
						Mage::getModel('contactlab_commons/task')->load($id)->runTask()->save();
						++$num;
					} catch (Exception $e) {
						$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while changing the status %1$s to %2$s.', $type, $id));
					}
					break;
			}
		}

		if ($num > 0) {
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been marked as %2$s.', $num, $type));
		}

		$this->_redirect('*/*');
	}

	/**
	 * Mass delete.
	 */
	public function massDeleteAction() {
		$session = Mage::getSingleton('adminhtml/session');
		$taskIds = $this->getRequest()->getParam("task_id");
		$num = 0;
		foreach ($taskIds as $id) {
			try {
				Mage::getModel('contactlab_commons/task')->load($id)->delete()->save();
				++$num;
			} catch (Exception $e) {
				$session->addException($e, Mage::helper('adminnotification')->__('An error occurred while deleting task %s.', $id));
			}
		}

		if ($num > 0) {
			$session->addSuccess(Mage::helper('adminnotification')->__('Total of %1$s record(s) have been deleted.', $num));
		}

		$this->_redirect('*/*');
	}

	/**
	 * Is this controller allowed?
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('newsletter/contactlab/tasks');
	}
}
