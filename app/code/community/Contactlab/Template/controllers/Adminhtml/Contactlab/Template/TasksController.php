<?php

class Contactlab_Template_Adminhtml_Contactlab_Template_TasksController
        extends Mage_Adminhtml_Controller_Action {

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed ()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('newsletter/template');
    }

    /**
     * List tasks action.
     */
    public function listAction() {
        $this->_title($this->__('Newsletter Templates'))->_title($this->__('XML Delivery Status'));
        $this->loadLayout();
        $templateId = $this->getRequest()->getParam('template_id');
        if (!$templateId) {
            $this->_redirectError("Invalid template id parameter");
        }
        Mage::register('template_id', $templateId);
        $this->_setActiveMenu('newsletter/template');
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Templates'),
            Mage::helper('newsletter')->__('XML Delivery Status'));

        $this->renderLayout();
    }

    /**
     * Queue detail action.
     */
    public function detailAction() {
        $this->_title($this->__('Newsletter Templates'))->_title($this->__('Queue Detail'));
        $queueId = $this->getRequest()->getParam('queue_id');
        if (!$queueId) {
            $this->_redirectError("Invalid queue id parameter");
        }
        $templateId = $this->getRequest()->getParam('template_id');
        if (!$templateId) {
            $this->_redirectError("Invalid template id parameter");
        }
        Mage::register('queue_id', $queueId);
        Mage::register('template_id', $templateId);
        $this->loadLayout();
        $this->_setActiveMenu('newsletter/template');
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Templates'),
            Mage::helper('newsletter')->__('Queue Detail'));

        $this->renderLayout();
    }
}
