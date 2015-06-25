<?php

/**
 * Configure link renderer.
 */
class Contactlab_Template_Block_Adminhtml_Type_Grid_Renderer_ConfigureLink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
        // TODO FIXME Differenziare wishlist e cart!
        switch ($row->getTemplateTypeCode()) {
            case 'CART':
                $url = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit",
                        array('section' => 'contactlab_template_cart'));
                break;
            case 'WISHLIST':
                $url = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit",
                        array('section' => 'contactlab_template_wishlist'));
                break;
            default:
                return "";
        }
        return sprintf("<a href=\"%2\$s\" title=\"%1\$s\">%1\$s</a>",
                $this->__('Configure'), $url);
    }

}
