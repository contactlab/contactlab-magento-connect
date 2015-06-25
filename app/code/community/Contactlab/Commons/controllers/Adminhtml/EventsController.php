<?php

/**
 * Events controller.
 */
class Contactlab_Commons_Adminhtml_EventsController extends Mage_Adminhtml_Controller_Action {

    /**
     * Index.
     */
    public function indexAction() {
        $this->_title($this->__('Events'));
        $this->loadLayout()->_setActiveMenu('newsletter/contactlab');
        return $this->renderLayout();
    }

    /**
     * Grid.
     */
    public function gridAction() {
        return $this->loadLayout(false)->renderLayout();
    }

}
