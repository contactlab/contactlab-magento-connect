<?php

/**
 * Test block to queue task.
 */
class Contactlab_Commons_Block_Adminhtml_Version extends Mage_Adminhtml_Block_Abstract {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->setTemplate("contactlab/commons/version.phtml");
        parent::__construct();
    }
    
    /**
     * Title of the block.
     * @return string
     */
    public function getTitle() {
        return $this->__("Plugin versions");
    }
    
    /**
     * Get module versions.
     * @return \Varien_Data_Collection
     */
    public function getModulesVersion() {
        /* @var $helper Contactlab_Commons_Helper_Data */
        $helper = Mage::helper('contactlab_commons');
        return $helper->getModulesVersion();
    }
    
    /**
     * Only in debug mode.
     * @return typeDo print version?
     */
    public function doPrintVersion() {
        /* @var $helper Contactlab_Commons_Helper_Data */
        $helper = Mage::helper('contactlab_commons');
        return $helper->isDebug();
    }

    /**
     * Get platform version.
     * @return String
     */
    public function getPlatformVersion() {
        return Mage::helper('contactlab_commons')->getPlatformVersion();
    }
}
