<?php

/**
 * Render for model columns.
 */
class Contactlab_Commons_Block_Adminhtml_Tasks_Renderer_Model
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
    	$open = "<span class=\"task-status-" . $task->getStatus() . "\" id=\"task-" . $task->getTaskId() . "\">";
		$close = "</span>";
    	$max = $task->getMaxValue();
    	if ($task->isRunning() && !empty($max)) {
    		$prog = $task->getProgressValue();
			$perc = 100 * $prog / $max;
    		$style = "margin-top: 4px; background: #699C69; border: 1px #3A634C solid; width: $perc%; display: block; height: 10px";
			$title = sprintf("%s %0.0f%% [%d/%d]", Mage::getSingleton($task->getModelName())->getName(), $perc, $prog, $max);
    		return $open . "<span style=\"$style\" title=\"$title\"/>" . $close;
    	} else {
        	return $open . Mage::getSingleton($task->getModelName())->getName() . $close;
    	}
	}
}
