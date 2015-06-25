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
        $rv = new Varien_Data_Collection();
        $count = 0;
        foreach (Mage::getConfig()->getNode('modules')->children() as $moduleName => $moduleConfig) {
            if (preg_match('/^Contactlab_.*/', $moduleName)) {
                $item = new Varien_Object();
                $item->setName(preg_replace('/^Contactlab_/', '', $moduleName))
                    ->setVersion((string) $moduleConfig->version)
                    ->setDescription((string) $moduleConfig->description);
                if ($count++ % 2 == 0) {
                    $item->setClass("even");
                }
                $rv->addItem($item);
            }
        }
        return $rv;
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
}
