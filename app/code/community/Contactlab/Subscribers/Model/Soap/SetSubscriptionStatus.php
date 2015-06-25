<?php

/**
 * Model for SetSubscriptionStatus calls.
 */
class Contactlab_Subscribers_Model_Soap_SetSubscriptionStatus extends Contactlab_Subscribers_Model_Soap_AbstractSubscriberCall {
	/** Do the SOAP call. */
	public function call() {
		$h = Mage::helper("contactlab_commons");
		$this->validateStatus();
		$subscriber = $this->_findSubscriberByEntityId($this->getEntityId());
		$clsField = $this->_getSubscribedFlagName();

		$h->logNotice(sprintf("Updating %s a %s",
				self::getAttributeFromAttributes($subscriber->attributes, 'EMAIL'),
				$this->getSubscriberStatus() ? "True" : "False"));
		if ((self::getAttributeFromAttributes($subscriber->attributes, $clsField) == 1) == $this->getSubscriberStatus()) {
			$h->logNotice(sprintf("%s was already in status %s",
					self::getAttributeFromAttributes($subscriber->attributes, 'EMAIL'),
					$this->getSubscriberStatus() ? "True" : "False"));
			return $this;
		}
		require_once('modifySubscriberSubscriptionStatus.php');
		require_once('Subscriber.php');

		$modifySubscriberSubscriptionStatus = new modifySubscriberSubscriptionStatus();
		$modifySubscriberSubscriptionStatus->token = $this->getAuthToken();
		$modifySubscriberSubscriptionStatus->webFormCode = $this->getConfig("contactlab_subscribers/global/web_form_code");
		$modifySubscriberSubscriptionStatus->subscriberIdentifier = $subscriber->identifier;
		$modifySubscriberSubscriptionStatus->isSubscribed = $this->getSubscriberStatus() ? 1 : 0;

		$rv = $this->getClient()->modifySubscriberSubscriptionStatus($modifySubscriberSubscriptionStatus);

		return $rv;
	}
}
