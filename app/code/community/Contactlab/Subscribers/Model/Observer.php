<?php

/**
 * Newsletter observer rewrite.
 */
class Contactlab_Subscribers_Model_Observer extends Mage_Core_Model_Abstract {

    private $_toUpdateUk = array();

    private $_toUnsubscribe = array();

    /**
     * Update customer stats, on order save and delete.
     * @param $observer
     */
    public function updateStats($observer) {
        try {
            Mage::helper("contactlab_subscribers")
                    ->updateCustomerStats($observer->getEvent()
                            ->getOrder()->getCustomerId());
        } catch (Exception $e) {
            Mage::log($e);
        }
    }

    /**
     * Update customer stats, on order save and delete.
     * @param $observer
     */
    public function doUpdateStats($observer) {
        try {
            Mage::helper("contactlab_subscribers")->doUpdateStats();
        } catch (Exception $e) {
            Mage::log($e);
        }
    }


    /**
     * A subscriber has been saved.
     * @param $observer
     */
	public function subscriberSaved($observer) {
		$subscriber = $observer->getEvent()->getDataObject();
		$isSubscribed = $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
        $uk = Mage::helper("contactlab_subscribers/uk")->searchBySubscriberId($subscriber->getSubscriberId());
        $i = new Varien_Object();
        $i->setIsSubscribed($isSubscribed);
        $i->setSubscriberEmail($subscriber->getSubscriberEmail());
        $i->setStoreId($subscriber->getStoreId());
        $this->_toUpdateUk[$uk->getEntityId()] = $i;
	}

    /**
     * A subscriber has been promoted to customer.
     * @param $observer Varien_Event_Observer
     * @return bool
     */
    public function subscriberPromotedToCustomer(Varien_Event_Observer $observer) {
        $uk = $observer->getEvent()->getDataObject();
        if (!$uk->hasSubscriberId()) {
            return false;
        }
        $subscriber = Mage::getModel('newsletter/subscriber')->load($uk->getSubscriberId());
        if (!$subscriber->hasSubscriberId()) {
            return false;
        }
        if (!Mage::getStoreConfigFlag("contactlab_subscribers/subscriber_to_customer/unsubscribe_if_not_confirm")) {
            return false;
        }
        $req = Mage::app()->getRequest();
        if (!$req->getPost('is_subscribed') && self::_isSubscribedLocally($subscriber)) {
            $this->_unsubscriberPromotedToCustomer($subscriber);
        }
    }

    /**
     * Unsubscribe promoted customer.
     * @param $subscriber Mage_Newsletter_Model_Subscriber
     */
    private function _unsubscriberPromotedToCustomer(Mage_Newsletter_Model_Subscriber $subscriber) {
        Mage::log("_unsubscriberPromotedToCustomer");
        Mage::helper('contactlab_subscribers')->setSkipSubscribeCustomer();
        $this->_toUnsubscribe[] = $subscriber->getId();
    }

    /**
     * Controller action post dispatch, consume uk record to update.
     * @param $observer
     */
    public function controllerActionPostdispatch($observer) {
        foreach ($this->_toUpdateUk as $k => $v) {
            Mage::helper("contactlab_subscribers")->updateSubscriberStatus(
                $v->getSubscriberEmail(), $k, $v->getIsSubscribed(), $v->getStoreId());
        }
        $this->_toUpdateUk = array();
        foreach ($this->_toUnsubscribe as $id) {
            $this->_doUnsubscribeLater(Mage::getModel('newsletter/subscriber')->load($id));
        }
        $this->_toUnsubscribe = array();
    }

    /**
     * Unsubscribe promoted customer.
     * @param $subscriber Mage_Newsletter_Model_Subscriber
     */
    private function _doUnsubscribeLater(Mage_Newsletter_Model_Subscriber $subscriber) {
        Mage::helper("contactlab_commons")->logNotice(sprintf("Subscriber \"%s\" has been promoted to customer without confirm subscription",
            $subscriber->getSubscriberEmail()));
        $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)->save();
        Mage::log(Mage::getModel('newsletter/subscriber')->load($subscriber->getId())->getData());
        if (Mage::getStoreConfigFlag("contactlab_subscribers/subscriber_to_customer/email_on_unsubscribe")) {
            $subscriber->sendUnsubscriptionEmail();
        }
        $uk = Mage::helper("contactlab_subscribers/uk")->searchBySubscriberId($subscriber->getId());
        Mage::helper("contactlab_subscribers")
            ->updateSubscriberStatus($subscriber->getEmail(),
            $uk->getEntityId(), false, $subscriber->getStoreId());
    }


    /**
     * Update last subscribed at, before save.
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @deprecated
     */
	private function _updateLastSubscribedAt(Mage_Newsletter_Model_Subscriber $subscriber) {
		$date = Mage::getModel('core/date')->gmtDate();
		$subscriber->setLastSubscribedAt($date);
	}

    /**
     * Was unsubscribed?
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return bool
     */
	private function _wasUnsubscribed(Mage_Newsletter_Model_Subscriber $subscriber) {
		$oldModel = new Mage_Newsletter_Model_Subscriber();
		$oldModel->load($subscriber->getSubscriberId());
		return $oldModel->getStatus() !== Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
	}

    /**
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return bool
     */
    private static function _isSubscribedLocally(Mage_Newsletter_Model_Subscriber $subscriber) {
        if ($subscriber->getId() && $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
            return true;
        }
        return false;
    }
}
