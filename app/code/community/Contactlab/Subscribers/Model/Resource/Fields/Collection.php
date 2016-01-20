<?php

/**
 * Additional Fields collection.
 */
class Contactlab_Subscribers_Model_Resource_Fields_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/newsletter_subscriber_fields");
    }
}

