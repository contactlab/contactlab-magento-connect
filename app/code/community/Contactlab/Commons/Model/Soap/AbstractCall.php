<?php

/**
 * Model for soap calls.
 */
abstract class Contactlab_Commons_Model_Soap_AbstractCall extends Mage_Core_Model_Abstract {
	public function singleCall(Contactlab_Commons_Model_Task $task = NULL) {
	    if (!is_null($task)) {
	       $this->setTask($task);
        }
		$this->startSession();
		try {
			$rv = $this->call();
		} catch (Exception $e) {
			$this->endSession();
			throw $e;
		}
		$this->endSession();
		return $rv;
	}

	/** Start session, borrow token*/
	public function startSession() {
		$this->validateStatus(false);
		$wsdl = $this->getConfig("contactlab_commons/soap/wsdl_url");
		if (empty($wsdl)) {
			throw new Zend_Exception("WSDL url not configured");
		}
		$apikey = $this->getConfig("contactlab_commons/soap/apikey");
		if (empty($apikey)) {
			throw new Zend_Exception("SOAP apikey not configured");
		}
		$userKey = $this->getConfig("contactlab_commons/soap/uid");
		if (empty($userKey)) {
			throw new Zend_Exception("SOAP UID not configured");
		}
		$this->setClient(new SoapClient($wsdl, array('soap_version' => SOAP_1_2)));

		$token = $this->getClient()->borrowToken(array('apiKey' => $apikey, 'userKey' => $userKey));
		$this->setAuthToken($token->return);
		//Mage::helper("contactlab_commons")->logDebug("SOAP token borrowed");
	}

    public function getConfig($path) {
        if ($this->hasStoreId()) {
            return Mage::getStoreConfig($path, $this->getStoreId());
        } else if ($this->hasTask()) {
            return $this->getTask()->getConfig($path);
        } else {
            return Mage::getStoreConfig($path);
        }
    }

    public function getConfigFlag($path) {
        if ($this->hasStoreId()) {
            return Mage::getStoreConfigFlag($path, $this->getStoreId());
        } else if ($this->hasTask()) {
            return $this->getTask()->getConfigFlag($path);
        } else {
            return Mage::getStoreConfigFlag($path);
        }
    }

	/** End session (invalidate token) */
	public function endSession() {
		$this->validateStatus();
		$rv = $this->getClient()->invalidateToken(array('token' => $this->getAuthToken()));
		//Mage::helper("contactlab_commons")->logDebug("SOAP Token Invalidated");
		return $rv;
	}

	/** Validate status. */
	protected function validateStatus($validateToken = true) {
		if (!$this->isSoapEnabled()) {
			throw new Zend_Exception("SOAP calls are disabled");
		}
		if ($validateToken) {
			if (!$this->hasAuthToken()) {
				throw new Zend_Exception("SOAP token not borrowed");
			}
		}
	}

	/** Is soap enabled? */
	public function isSoapEnabled() {
		return $this->getConfigFlag("contactlab_commons/soap/enable");
	} 
	
	/**
	 * @return SoapClient
	 */
	public function getClient() {
		return parent::getClient();
	}

	/** Do the call. */
	public abstract function call();

	/** get subscriber attribute from key and value. */
	public static function getSubscriberAttributeFromKeyValue($key, $value) {
		require_once('SubscriberAttribute.php');
		$attribute = new SubscriberAttribute();
		$attribute->key = $key;
		if (is_object($value)) {
	        switch (get_class($value)) {
	            case "DateTime":
    	            $value = $value->format('Y-m-d H:i:s');
        	        break;
	        }
		}
		$attribute->value = new \SoapVar($value, XSD_ANYTYPE, "string", 'http://www.w3.org/2001/XMLSchema', 'value');
	    return $attribute;
	}

	public static function getAttributeFromAttributes(array &$attributes, $key) {
		foreach ($attributes as $attribute) {
			if ($attribute->key === $key) {
				return $attribute->value;
			}
		}
		return false;
	}

	public static function setAttributeFromAttributes(array &$attributes, $key, $value) {
		foreach ($attributes as $attribute) {
			if ($attribute->key === $key) {
				$attribute->value = $value;
				return true;
			}
		}
		return false;
	}
}

// Set include path with libs
set_include_path(get_include_path() . PS . Mage::getBaseDir('lib') . DS . 'contactlab');
