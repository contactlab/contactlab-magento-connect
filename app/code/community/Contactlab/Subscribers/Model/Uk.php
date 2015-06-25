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
        /* @var $resource Contactlab_Subscribers_Model_Resource_Uk */
        $resource = $this->getResource();
        $resource->setTask($this->getTask());
        $resource->update($doit);
        if ($resource->getHasNotices()) {
            $this->setHasNotices(true);
        }
    }

    /** Truncate table. */
    public function truncate() {
        /* @var $resource Contactlab_Subscribers_Model_Resource_Uk */
        $resource = $this->getResource();
        $resource->setTask($this->getTask());
        $resource->truncate();
    }
}
