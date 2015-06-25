<?php

/**
 * Abstract call with findSubscriber method.
 */
abstract class Contactlab_Subscribers_Model_Soap_AbstractSubscriberCall extends Contactlab_Commons_Model_Soap_AbstractCall {
	protected function _findSubscriberByEntityId($id) {
        return $this->_findSubscriber('entity_id', $id);
    }

	protected function _findSubscriberByEmail($email) {
        return $this->_findSubscriber('email', $email);
    }

	private function _findSubscriber($field, $value) {
		$h = Mage::helper("contactlab_commons");

		require_once('LookupPreferences.php');
		require_once('findSubscribers.php');

		$sourceIdentifier = $this->getConfig("contactlab_subscribers/global/source_identifier");

        $lookupPrefs = new LookupPreferences();
		$lookupPrefs->pageNumber = 1;
		$lookupPrefs->matchingMode = "EQUALS";
		$lookupPrefs->sortingMode = "ASCENDING";

		$findSubscribers = new findSubscribers();
		$findSubscribers->token = $this->getAuthToken();
		$findSubscribers->sourceIdentifier = $sourceIdentifier;
		$findSubscribers->lookupPrefs = $lookupPrefs;
        $findSubscribers->attribute = self::getSubscriberAttributeFromKeyValue($field, $value);

		$subscribers = $this->getClient()->findSubscribers($findSubscribers)->return;
		if (!$subscribers || empty($subscribers)) {
			throw new Contactlab_Subscribers_Model_Soap_SubscriberNotFoundException();
		}
		if (!isset($subscribers->currentPageItems)) {
			throw new Contactlab_Subscribers_Model_Soap_SubscriberNotFoundException();
		}
		$currentPageItems = $subscribers->currentPageItems;
		if (!$currentPageItems || empty($currentPageItems)) {
			throw new Contactlab_Subscribers_Model_Soap_SubscriberNotFoundException();
		}
		if (is_array($currentPageItems)) {
			$currentPageItems = $currentPageItems[0];
		}
		return $currentPageItems;
	}

    /** Get subscription flag name. */
    protected function _getSubscribedFlagName() {
        return $this->getConfig("contactlab_subscribers/global/subscribed_flag_name");
    }
}
