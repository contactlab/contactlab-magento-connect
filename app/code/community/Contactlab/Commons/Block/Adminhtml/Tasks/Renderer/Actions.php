<?php

/**
 * Render for model columns.
 */
class Contactlab_Commons_Block_Adminhtml_Tasks_Renderer_Actions
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
    	return self::renderTask($row);
    }

	public static function renderTask(Contactlab_Commons_Model_Task $task) {
    	$open = "<span class=\"task-status-" . $task->getStatus() . "\" id=\"task-action-" . $task->getTaskId() . "\">";
		$close = "</span>";
		$rv = "";
		self::_addAction($rv, $task, 'cancel', "Cancel task", $task->canCancel());
		self::_addAction($rv, $task, 'delete', "Delete task", $task->canDelete());
		self::_addAction($rv, $task, 'suspend', "Suspend task", $task->canSuspend());
		self::_addAction($rv, $task, 'unsuspend', "Unsuspend task", $task->canUnsuspend());
		self::_addAction($rv, $task, 'retry', "Retry", true);
		self::_addAction($rv, $task, 'run', "Run now", $task->canRun());
		return $open . $rv . $close;
	}

	public static function _addAction(&$rv, Contactlab_Commons_Model_Task $task, $code, $title, $enabled) {
		$s = "";
		$title = Mage::helper('contactlab_commons')->__($title);
		if (!$enabled) {
			$s = "style=\"opacity:0.2; cursor: default;\" ";
		}
		$img = sprintf("<img %ssrc=\"%s\" title=\"%s\" alt=\"%s\"/>",
			$s,
			Mage::getDesign()->getSkinUrl('images/contactlab/commons/' . $code . '.png'),
			$title, $title);
		if ($enabled) {
			$rv .= sprintf(" <a href=\"%s\" title=\"%s\">%s</a>",
					Mage::helper('adminhtml')->getUrl('*/*/' . $code, array('task_id' => $task->getTaskId())), $title, $img);
		} else {
			$rv .= sprintf(" %s", $img);
		}
	}
}
