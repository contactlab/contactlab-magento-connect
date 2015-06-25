<?php

/**
 * Customer Statistics controller.
 */
class Contactlab_Subscribers_Adminhtml_StatsController extends Mage_Adminhtml_Controller_Action {

    /**
     * Queue action.
     */
    public function clearAction() {
        Mage::helper("contactlab_subscribers")->addClearStatsQueue();
        return $this->_redirect('contactlab_commons/adminhtml_tasks');
    }

    /**
     * Queue action.
     */
    public function fillAction() {
        Mage::helper("contactlab_subscribers")->addCalcStatsQueue();
        return $this->_redirect('contactlab_commons/adminhtml_tasks');
    }
}
