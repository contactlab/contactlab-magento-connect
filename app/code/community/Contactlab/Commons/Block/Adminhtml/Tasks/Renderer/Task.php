<?php

/**
 * Render for task_id column.
 */
class Contactlab_Commons_Block_Adminhtml_Tasks_Renderer_Task
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        if (!$row->hasTaskId()) {
            return "";
        }
        $model = Mage::getModel('contactlab_commons/task')->load($row->getTaskId());
    	return sprintf('<a href="%s" title="%s">%s</a>',
    	    $model->getEventsUrl(),
    	    (string) $model,
    	    $model->getDescription());
    }
}
