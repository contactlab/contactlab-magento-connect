<?php

/**
 * Logs block.
 */
class Contactlab_Commons_Block_Adminhtml_Logs extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_commons';
        $this->_controller = 'adminhtml_logs';
        $this->_headerText = $this->__("Logs");

        parent::__construct();
        $this->removeButton("add");
        $url = $this->getUrl("*/*/truncate");
        $message = Mage::helper("contactlab_commons")->__("Are you sure to empty log table?");
        $this->addButton("truncate", array(
            'label' => Mage::helper("contactlab_commons")->__("Truncate log table"),
            'onclick' => 'deleteConfirm(\'' . $message . '\', \'' . $url . '\')'
        ));
    }

}
