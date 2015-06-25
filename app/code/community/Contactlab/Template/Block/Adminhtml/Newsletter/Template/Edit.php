<?php

/**
 * Adminhtml Newsletter Template Edit Form Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Edit extends Mage_Adminhtml_Block_Newsletter_Template_Edit {
    /**
     * Overrides prepare layout
     */
    protected function _prepareLayout() {
        $rv = parent::_prepareLayout();

        // Remove unused buttons
        $this->unsetChild('preview_button');
        $this->unsetChild('to_html_button');
        $this->unsetChild('to_plain_button');

        // Return
        return $rv;
    }
}
