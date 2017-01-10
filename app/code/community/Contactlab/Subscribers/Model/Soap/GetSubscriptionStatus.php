<?php

/**
 * Model for GetSubscriptionStatus calls.
 */
class Contactlab_Subscribers_Model_Soap_GetSubscriptionStatus extends Contactlab_Subscribers_Model_Soap_AbstractSubscriberCall {
	/** Do the SOAP call. */
	public function call() {
		$this->validateStatus();
        if ($this->hasSubscriberId()) {
            $uk = Mage::helper("contactlab_subscribers/uk")->searchBySubscriberId($this->getSubscriberId());
        } else if ($this->hasCustomerId()) {
            $uk = Mage::helper("contactlab_subscribers/uk")->searchByCustomerId($this->getCustomerId());
        }
        if ($uk)
        {
        	if($uk->hasEntityId()) {
		    	$subscriber = $this->_findSubscriberByEntityId($uk->getEntityId());
        	}
        } else {
            Mage::log("ERROR: Could not find uk for " . $this->getSubscriberEmail());
		    $subscriber = $this->_findSubscriberByEmail($this->getSubscriberEmail());
        }
		$clsField = $this->_getSubscribedFlagName();
		$rv = self::getAttributeFromAttributes($subscriber->attributes, 'EMAIL') === $this->getSubscriberEmail();
		if ($rv) {
			$subscribed = self::getAttributeFromAttributes($subscriber->attributes, $clsField);
			$rv = $subscribed === 1;
		}
		return $rv;
	}
}
