<?php

/**
 * Test controller.
 */
class Contactlab_Commons_Adminhtml_ReleaseNotesController extends Mage_Adminhtml_Controller_Action {

    /**
     * Index of release notes.
     */
    public function indexAction() {
        $this->_title($this->__('ContactLab release notes'));
        $this->loadLayout()->_setActiveMenu('newsletter/contactlab');
        return $this->renderLayout();
    }
}
