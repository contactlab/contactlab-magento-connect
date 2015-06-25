<?php

/**
 * Model for StartSubscriberDataExchangeCall calls.
 */
class Contactlab_Subscribers_Model_Soap_StartSubscriberDataExchangeCall extends Contactlab_Commons_Model_Soap_AbstractCall {
	/** Do the SOAP call. */
	public function call() {
		$this->validateStatus();
		Mage::helper("contactlab_commons")->logNotice("Calling StartSubscriberDataExchangeCall via SOAP");
		$dataExchangeConfigIdentifier = $this->getTask()->getConfig("contactlab_commons/soap/data_updater_config_identifier");
		$rv = $this->getClient()->startSubscriberDataExchange(array(
			'token' => $this->getAuthToken(),
			'dataExchangeConfigIdentifier' => $dataExchangeConfigIdentifier,
		));
		Mage::helper("contactlab_commons")->logNotice("StartSubscriberDataExchangeCall called");
		return json_encode($rv);
	}
}
