<?php

/**
 * Fields resource.
 */
class Contactlab_Subscribers_Model_Resource_Fields extends Mage_Core_Model_Mysql4_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/newsletter_subscriber_fields", "entity_id");
    }


}
