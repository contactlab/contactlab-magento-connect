<?php

/**
 * Events block.
 */
class Contactlab_Commons_Block_Adminhtml_Events extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_commons';
        $this->_controller = 'adminhtml_events';
        $this->_headerText = $this->__("Events");

        parent::__construct();
        $this->removeButton("add");
        $this->_addBackButton();
    }
    
    /** To task index. */
    public function getBackUrl() {
        return $this->getUrl('*/contactlab_commons_tasks');
    }
}
