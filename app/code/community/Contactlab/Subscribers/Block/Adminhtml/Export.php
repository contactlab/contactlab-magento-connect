<?php

/**
 * Export block.
 */
class Contactlab_Subscribers_Block_Adminhtml_Export extends Mage_Adminhtml_Block_Widget_Container {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_subscribers';
        $this->_controller = 'adminhtml_subscribers';
        $this->_headerText = $this->__("Export subscribers");
        $this->setTemplate("contactlab/subscribers/export.phtml");
        parent::__construct();
        $this->addButton("queue", array(
            'label' => $this->__("Queue Subscribers export job"),
            'onclick' => 'deleteConfirm(\''
                . $this->__('Are you sure you want to do this?')
                . '\', \'' . Mage::helper('adminhtml')->getUrl('*/*/queue') . '\')'
        ));
    }

}
