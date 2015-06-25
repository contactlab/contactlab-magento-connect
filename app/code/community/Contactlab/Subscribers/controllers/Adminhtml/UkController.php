<?php

/**
 * Controller that manage the queue of exporting tasks.
 */
class Contactlab_Subscribers_Adminhtml_UkController extends Mage_Adminhtml_Controller_Action {
    /**
     * Update UK Table action.
     */
    public function updateAction() {
        $doIt = Mage::app()->getRequest()->getParam('doit') === 'yes';
        $helper = Mage::helper('contactlab_subscribers');
        $session = Mage::getSingleton('adminhtml/session');
        try {
            if (!Mage::helper('contactlab_commons')->isAllowed('uk', 'update')) {
                throw new Zend_Exception("Unique keys update not allowed");
            }
            $this->_doUpdate($doIt);
            $session->addSuccess($helper->__('Unique keys updated successfully.'));
        } catch (Exception $e) {
            $session->addError($e);
        }
        return $this->_redirect('contactlab_commons/adminhtml_tasks');
    }

    /** Really update uk. */
    private function _doUpdate($doIt) {
        Mage::getModel("contactlab_subscribers/uk")->update($doIt);
    }
}
