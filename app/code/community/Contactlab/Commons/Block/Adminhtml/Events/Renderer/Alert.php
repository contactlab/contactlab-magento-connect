<?php

/**
 * Alert renderer.
 */
class Contactlab_Commons_Block_Adminhtml_Events_Renderer_Alert extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
        return $row->getSendAlert()
                ? sprintf("<strong style=\"color: darkred\">%s</strong>",
                        Mage::helper('contactlab_commons')->__("Yes"))
                : sprintf("<strong style=\"color: darkgreen\">%s</strong>",
                        Mage::helper('contactlab_commons')->__("No"));
    }

}
