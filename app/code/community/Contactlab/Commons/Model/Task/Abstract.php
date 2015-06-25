<?php

/**
 * Abstract Task.
 */
abstract class Contactlab_Commons_Model_Task_Abstract extends Mage_Core_Model_Abstract
        implements Contactlab_Commons_Model_Task_Interface {

    /**
     * Run the task.
     */
    public final function run() {
        $this->_beforeRun();
        $rv = $this->_runTask();
        $this->_afterRun();
        return $rv;
    }

    /**
     * Called before the run.
     */
    protected function _beforeRun() {
    }

    /**
     * Mermory limit.
     * @return Memory limit or void if not to be modified.
     */
    public function getMemoryLimit() {
        return "";
    }


    /**
     * Really run the task, implemented in classes.
     */
    protected abstract function _runTask();

    /**
     * Called after the run.
     */
    protected function _afterRun() {
        
    }

    /** Default retries interval. */
    public function getDefaultRetriesInterval() {
        return $this->getTask()->getConfig($this->_getConfigPathFor("interval"));
    }

    /** Max number of retries. */
    public function getDefaultMaxRetries() {
        return $this->getTask()->getConfig($this->_getConfigPathFor("retries"));
    }
    
    private function _getConfigPathFor($configSuffixName) {
        $prefix = strtolower(preg_replace("|^(.*)_Model_.*|", "$1", get_class($this)));
        return sprintf("%s/queue/%s_%s", $prefix,
                strtolower(get_class($this)), $configSuffixName);
    }

	/**
	 * Check Subscriber Data Exchange Status.
	 */
	protected function _checkSubscriberDataExchangeStatus() {
		$call = Mage::getModel('contactlab_commons/soap_getSubscriberDataExchangeStatus');
		$status = $call->singleCall($this->getTask());
		
		if ($status == 'INREQUEST' || $status == 'RUNNING' || $status == 'RETRY') {
			throw new Zend_Exception("Subscriber data exchange is in status $status, can't continue");
		}
		if ($status == 'FAILED' || $status == 'TIMED_OUT') {
			$this->getTask()->addEvent("Subscriber data exchange is in status $status, going on");
		}
	}
}
