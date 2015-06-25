<?php

/**
 * Transactional email data helper.
 */
class Contactlab_Transactional_Model_Zend_Mail extends Zend_Mail {
	public $enqueue = true;

	private $_bccTmp = array();
	private $_ccTmp = array();
	private $_toTmp = array();

    private $_templateCode = "";
    private $_templateId = "";

	/**
     * Public constructor
     *
     * @param  string $charset
     * @return void
     */
    public function __construct($charset = null)
    {
        parent::__construct($charset);
    }

    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param  Zend_Mail_Transport_Abstract $transport
     * @return Zend_Mail                    Provides fluent interface
     */
    public function reallySend(Contactlab_Commons_Model_Task $task)
    {
        $transport = new Zend_Mail_Transport_Smtp(
            $task->getConfig("contactlab_transactional/global/smtp"));			
    	parent::send($transport);
	}

    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param  Zend_Mail_Transport_Abstract $transport
     * @return Zend_Mail                    Provides fluent interface
     */
    public function send($transport = null)
    {
		$this->_addCustomHeaders();
        // TODO always store id 0?
    	$this->_queueCcAndBcc(Mage::app()->getStore()->getStoreId());
		return $this;
	}

    /**
     * Adds Bcc recipient, $email can be an array, or a single string address
     *
     * @param  string|array    $email
     * @return Zend_Mail Provides fluent interface
     */
    public function addBcc($email)
    {
    	$this->_bccTmp[] = $email;
		return $this;
	}

    /**
     * Adds To-header and recipient, $email can be an array, or a single string address
     *
     * @param  string|array $email
     * @param  string $name
     * @return Zend_Mail Provides fluent interface
     */
    public function reallyAddTo($email, $name='')
    {
    	parent::addTo($email, $name);
	}

    /**
     * Adds To-header and recipient, $email can be an array, or a single string address
     *
     * @param  string|array $email
     * @param  string $name
     * @return Zend_Mail Provides fluent interface
     */
    public function addTo($email, $name='')
    {
    	$this->_toTmp[] = array('email' => $email, 'name' => $name);
	}

    /**
     * Adds Bcc recipient, $email can be an array, or a single string address
     *
     * @param  string|array    $email
     * @return Zend_Mail Provides fluent interface
     */
    public function addCc($email, $name='')
    {
    	$this->_ccTmp[] = array('email' => $email, 'name' => $name);
		return $this;
	}

	/**
	 * Queue Cc and Bcc recipients emails.
	 * 
     * @return Zend_Mail Provides fluent interface
	 */
	protected function _queueCcAndBcc($storeId) {
		// Copy to local variables
		$to = $this->_toTmp;
		$cc = $this->_ccTmp;
		$bcc = $this->_bccTmp;

		// Empty variables
		$this->_toTmp = array();
		$this->_ccTmp = array();
		$this->_bccTmp = array();

		$this->_queueArray($to, $storeId);
		$this->_queueArray($cc, $storeId);
		$this->_queueArray($bcc, $storeId);

		return $this;
	}

	/**
	 * Queue array of emails.
	 * 
     * @return Zend_Mail Provides fluent interface
	 */
	protected function _queueArray(array &$emails, $storeId) {
		foreach ($emails as $email) {
			$this->_queueEmail($email, $storeId);
		}
	}

	/**
	 * Queue array of emails.
	 * 
     * @return Zend_Mail Provides fluent interface
	 */
	protected function _queueEmail($email, $storeId) {
		$newMail = clone $this;
		if (is_array($email)) {
			$newMail->reallyAddTo($email['email'], $email['name']);
			$emailDescription = $email['email'];
		} else {
			$newMail->reallyAddTo($email);
			$emailDescription = $email;
		}
        Mage::getModel("contactlab_commons/task")
                ->setStoreId($storeId)
                ->setTaskCode("Transactional email to $emailDescription")
                ->setModelName('contactlab_transactional/task_sendEmailRunner')
                ->setDescription("Send transactional email to $emailDescription")
				->setTaskData(serialize($newMail))
                ->save();
	}

    private function _crc16($string) {
        $crc = 0xFFFF;
        for ($x = 0; $x < strlen($string); $x++) {
            $crc = $crc ^ ord($string[$x]);
            for ($y = 0; $y < 8; $y++) {
                if (($crc & 0x0001) == 0x0001) {
                    $crc = (($crc >> 1) ^ 0xA001);
                } else {
                    $crc = $crc >> 1;
                }
            }
        }
        return $crc;
    }

	/**
	 * Adds Contactlab Custom Headers
	 */
	protected function _addCustomHeaders() {
		// FIXME ExternalDesc is not into the mail..
        //$this->addHeader("X-Clab-SmartRelay-External", $this->_templateId);
        //$this->addHeader("X-Clab-SmartRelay-ExternalDesc", $this->_templateCode);

        $this->addHeader("X-Clab-SmartRelay-DeliveryId",    sprintf("%u", $this->_crc16($this->_templateCode)));
        $this->addHeader("X-Clab-SmartRelay-DeliveryLabel", $this->_templateCode);
        // Mage::log("Template code is: ". $this->_templateCode, null, null, true);
	}
    
    public function setTemplateCode($value) {
        $this->_templateCode = $value;
    }

    public function setTemplateId($value) {
        $this->_templateId = $value;
    }
}
