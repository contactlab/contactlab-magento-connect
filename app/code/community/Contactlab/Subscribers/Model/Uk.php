<?php

/**
 * Uk table model.
 */

/**
 * @method bool hasEntityId()
 * @method int getEntityId()
 * @method Contactlab_Subscribers_Model_Uk setEntityId(int $value)
 * @method int getSubscriberId()
 * @method Contactlab_Subscribers_Model_Uk setSubscriberId(int $value)
 * @method int getCustomerId()
 * @method Contactlab_Subscribers_Model_Uk setCustomerId(int $value)
 * @method int getIsExported()
 * @method Contactlab_Subscribers_Model_Uk setIsExported(int $value)
 * @method Contactlab_Subscribers_Model_Uk setHasNotices(bool $value)
 * @method bool getHasNotices()
 * @method Contactlab_Commons_Model_Task getTask()
 * @method Contactlab_Subscribers_Model_Uk setTask(Contactlab_Commons_Model_Task $value)
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
     * @param bool|true $doIt
     */
    public function purge($doIt = true) {
        $this->getResource()->purge($doIt);
    }

    /**
     * Update keys.
     * @param bool|false $doIt
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
