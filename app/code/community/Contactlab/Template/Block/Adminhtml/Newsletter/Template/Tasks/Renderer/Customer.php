<?php

class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_Renderer_Customer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if (!$row->hasCustomerId()) {
            return '';
        }
        $customer = Mage::getModel('customer/customer')->load($row->getCustomerId());
        return sprintf('<a href="%s">%s [<strong>%s</strong>]</a>',
            Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId())),
            $customer->getName(), $customer->getEmail());
    }

}