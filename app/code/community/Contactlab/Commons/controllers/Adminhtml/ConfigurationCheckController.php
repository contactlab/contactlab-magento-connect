<?php

/**
 * Test controller.
 */
class Contactlab_Commons_Adminhtml_ConfigurationCheckController extends Mage_Adminhtml_Controller_Action {

    /**
     * Index of release notes.
     */
    public function indexAction() {
        $this->_title($this->__('Cron Configuration Check'));
        $this->loadLayout()->_setActiveMenu('newsletter/contactlab');
        return $this->renderLayout();
    }
}
