<?php

/**
 * Customer Statistics controller.
 */
class Contactlab_Subscribers_Adminhtml_Contactlab_Subscribers_StatsController extends Mage_Adminhtml_Controller_Action {

    /**
     * Queue action.
     * @deprecated
     */
    public function clearAction() {
        Mage::helper("contactlab_subscribers")->addClearStatsQueue();
        return $this->_redirect('adminhtml/contactlab_commons_tasks');
    }

    /**
     * Queue action.
     */
    public function fillAction() {
        Mage::helper("contactlab_subscribers")->addCalcStatsQueue();
        return $this->_redirect('adminhtml/contactlab_commons_tasks');
    }

    /**
     * Is this controller allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('newsletter/contactlab/stats');
    }
}
