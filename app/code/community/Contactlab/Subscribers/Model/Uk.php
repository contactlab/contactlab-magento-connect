<?php

/**
 * Uk table model.
 */
class Contactlab_Subscribers_Model_Uk extends Mage_Core_Model_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/uk");
    }
    
    /** Remove null null records. */
    public function purge($doit = true) {
        $this->getResource()->purge($doit);
    }

    /** Update keys. */
    public function update($doit = false) {
        $this->getResource()->update($doit);
    }
}
