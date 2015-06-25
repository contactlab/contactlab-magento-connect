<?php

/**
 * Level renderer.
 */
class Contactlab_Commons_Block_Adminhtml_Logs_Renderer_Level
        extends Contactlab_Commons_Block_Adminhtml_Logs_Renderer_Default {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
        $level = $this->_getValue($row);
        $name = Contactlab_Commons_Helper_Data::$LEVELS [$level];
        return sprintf('<span style="%s">%s</span>', $this->_getStyle($row->getLogLevel()), $name);
    }

}
