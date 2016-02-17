<?php

/**
 * Class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_Detail
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_Detail
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_template';
        $this->_controller = 'adminhtml_newsletter_template_tasks_detail';
        $this->_headerText = $this->__("Queue Detail");

        parent::__construct();
        $this->_removeButton('add');
        $this->_addBackButton();
    }

    /**
     * Back url to newsletter template page.
     * @return string
     */
    public function getBackUrl() {
        return $this->getUrl('*/*/list',
            array('template_id' => Mage::registry('template_id')));
    }
}