<?php

/**
 * Logs block.
 */
class Contactlab_Template_Block_Adminhtml_Type extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_template';
        $this->_controller = 'adminhtml_type';
        $this->_headerText = $this->__("Template types");

        parent::__construct();
    }
}
