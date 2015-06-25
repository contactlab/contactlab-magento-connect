<?php

/**
 * Test block to queue task.
 */
class Contactlab_Commons_Block_Adminhtml_Test extends Mage_Adminhtml_Block_Widget_Container {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_commons';
        $this->_controller = 'adminhtml_test';
        $this->_headerText = $this->__("Tasks");
        $this->setTemplate("contactlab/commons/test.phtml");
        parent::__construct();
        $this->addButton("queue", array(
            'label' => $this->__("Queue task job"),
            'onclick' => 'deleteConfirm(\''
            . $this->__('Are you sure you want to do this?') . '\', \''
            . Mage::helper('adminhtml')->getUrl('*/*/queue') . '\')'
        ));
    }
}
