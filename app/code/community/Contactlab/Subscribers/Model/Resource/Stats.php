<?php

/**
 * Exporter data helper.
 */
class Contactlab_Subscribers_Model_Resource_Stats extends Mage_Core_Model_Mysql4_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/stats", "entity_id");
    }

    /** Clear all stats. */
    public function clear() {
        Mage::helper("contactlab_commons")->logNotice("Clear statistics");
        $this->_getWriteAdapter()->delete($this->getMainTable());
    }
}
