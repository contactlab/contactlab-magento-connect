<?php

/**
 * Datetime renderer
 */
class Contactlab_Commons_Block_Adminhtml_Logs_Renderer_Datetime
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
        if ($this->_getValue($row) === '0000-00-00 00:00:00') {
            return "";
        }
        return sprintf('<span style="%s">%s</span>',
                $this->_getStyle($row->getLogLevel()), parent::render($row));
    }

    /**
     * Gest style from key value.
     */
    protected function _getStyle($value) {
        return Contactlab_Commons_Block_Adminhtml_Logs_Renderer_Default::$STYLES [$value];
    }

}
