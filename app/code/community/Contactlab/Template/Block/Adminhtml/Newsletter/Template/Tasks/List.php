<?php

class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_List
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_template';
        $this->_controller = 'adminhtml_newsletter_template_tasks_list';
        $this->_headerText = $this->__("XML Delivery Status");

        parent::__construct();
        $this->_removeButton('add');
    }
}