<?php

/**
 * Controller that manage the queue of exporting tasks.
 */
class Contactlab_Subscribers_Adminhtml_ExportSubscribersController extends Mage_Adminhtml_Controller_Action {
    /**
     * Queue action.
     */
    public function queueAction() {
        Mage::getModel("contactlab_subscribers/cron")->addExportSubscribersQueue();
        return $this->_redirect('contactlab_commons/adminhtml_tasks');
    }

    /**
     * Is this controller allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('newsletter/contactlab/subscribers/actions/queue');
    }
}
