<?php

/**
 * Status renderer.
 */
class Contactlab_Commons_Block_Adminhtml_Events_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
    	return self::renderTask($row);
    }

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public static function renderTask(Varien_Object $row) {
        $h = Mage::helper("contactlab_commons");
    	$open = "<span class=\"task-status-" . $row->getStatus() . "\" id=\"task-status-" . $row->getTaskId() . "\">";
		$close = "</span>";
        $status = $row->getStatus();
        $name = Contactlab_Commons_Model_Task::$statuses[$status];
        return sprintf($open . "<span title=\"%s\" style=\"color: %s; %s\">%s</span>" . $close,
                $h->__($name['description']), $name['color'], $name['style'],
                    $h->__($status));
    }

}
