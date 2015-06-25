<?php

/**
 * Test controller.
 */
class Contactlab_Commons_Adminhtml_TestController extends Mage_Adminhtml_Controller_Action {

    /**
     * Index.
     */
    public function indexAction() {
        $this->_title($this->__('Test job'));
        $this->loadLayout()->_setActiveMenu('newsletter/contactlab');
        return $this->renderLayout();
    }

    /**
     * Queue action.
     */
    public function queueAction() {
        Mage::getModel("contactlab_commons/cron")->addTestQueue();
        return $this->_redirect('*/adminhtml_tasks');
    }

}
