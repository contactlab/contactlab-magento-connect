<?php

/**
 * Controller that manage the queue of exporting tasks.
 */
class Contactlab_Subscribers_Adminhtml_ImportSubscribersController extends Mage_Adminhtml_Controller_Action {
    /**
     * Queue action.
     */
    public function queueAction() {	
        Mage::getModel("contactlab_subscribers/cron")->addImportSubscribersQueue();
        return $this->_redirect('contactlab_commons/adminhtml_tasks');
    }
}
