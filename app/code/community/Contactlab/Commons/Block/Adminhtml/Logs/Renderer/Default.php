<?php

/**
 * Default renderer.
 */
class Contactlab_Commons_Block_Adminhtml_Logs_Renderer_Default
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    static $STYLES = array(
        Zend_Log::EMERG => "color: red",
        Zend_Log::ALERT => "color: red",
        Zend_Log::CRIT => "color: red",
        Zend_Log::ERR => "color: red",
        Zend_Log::WARN => "color: orange",
        Zend_Log::NOTICE => "color: #868636",
        Zend_Log::INFO => "color: #999",
        Zend_Log::DEBUG => "color: #666"
    );

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
        return sprintf('<span style="%s">%s</span>',
                $this->_getStyle($row->getLogLevel()), parent::render($row));
    }

    /**
     * Get style from value key.
     */
    protected function _getStyle($value) {
        return self::$STYLES [$value];
    }

}
