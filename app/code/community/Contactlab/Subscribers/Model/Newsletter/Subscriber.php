<?php

/**
 * Newsletter subscribers model, get and set from SOAP.
 */
class Contactlab_Subscribers_Model_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber {
    // Is customer subscribed?
    public function isSubscribed()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return parent::isSubscribed();
        }
        if (!Mage::getStoreConfigFlag("contactlab_subscribers/global/soap_call_is_subscribed")) {
            return parent::isSubscribed();
        }
        try {
            $call = Mage::getModel('contactlab_subscribers/soap_getSubscriptionStatus');
            if ($this->hasSubscriberEmail()) {
                $email = $this->getSubscriberEmail();
            } else if ($this->hasSavedCustomerEmail()) {
                $email = $this->getSavedCustomerEmail();
            }
            $call->setStoreId($this->getStoreId());
            if ($this->hasSubscriberId()) {
                $call->setSubscriberId($this->getSubscriberId());
            } else if ($this->hasSavedCustomerId()) {
                $call->setCustomerId($this->getSavedCustomerId());
            }
            $call->setSubscriberEmail($email);
            $rv = $call->singleCall();
            return $rv;
        } catch (Contactlab_Subscribers_Model_Soap_SubscriberNotFoundException $e) {
            return parent::isSubscribed();
        } catch (Exception $e) {
            Mage::helper("contactlab_commons")->logCrit($e->getMessage());
            return parent::isSubscribed();
        }
        return parent::isSubscribed();
    }

    /**
     * Load subscriber by customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    public function loadByCustomer(Mage_Customer_Model_Customer $customer) {
        $this->setSavedCustomerId($customer->getEntityId());
        $this->setSavedCustomerEmail($customer->getEmail());
        return parent::loadByCustomer($customer);
    }    

    /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer) {
        if (Mage::helper('contactlab_subscribers')->skipSubscribeCustomer()) {
            return;
        }
        return parent::subscribeCustomer($customer);
    }
    
    /**
     * Unsubscribes loaded subscription
     *
     */
    public function unsubscribe()
    {
        parent::unsubscribe();
        $this->setLastSubscribedAt(null)->save();
    }

    /**
     * Unsubscribes loaded subscription
     *
     */
    public function subscribe($email)
    {
        parent::subscribe($email);
        $this->setLastSubscribedAt(Mage::getModel('core/date')->gmtDate())->save();
    }

    protected function _beforeSave()
    {
        $this->setLastUpdatedAt(Mage::getModel('core/date')->gmtDate());
        parent::_beforeSave();
    }
}
