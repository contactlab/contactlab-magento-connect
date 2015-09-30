<?php

/**
 * Uk table model.
 * @method bool getSubscriberId()
 * @method bool hasEntityId()
 * @method bool hasSubscriberId()
 * @method Contactlab_Commons_Model_Task getTask()
 * @method setHasNotices($true)
 */
class Contactlab_Subscribers_Model_Uk extends Mage_Core_Model_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/uk");
    }
    
    /**
     * Remove null null records.
     * @param bool $doIt
     */
    public function purge($doIt = true) {
        $this->getResource()->purge($doIt);
    }

    /**
     * Update keys.
     * @param bool $doIt
     */
    public function update($doIt = false) {
        /* @var $resource Contactlab_Subscribers_Model_Resource_Uk */
        $resource = $this->getResource();
        $this->setHasNotices(false);
        $resource->setHasNotices(false);
        $resource->setTask($this->getTask());
        $resource->update($doIt);
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
