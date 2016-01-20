<?php

/**
 * Fields table model.
 * @method getNotes()
 * @method getFirstName()
 * @method getLastName()
 * @method getCustom1()
 * @method getCustom2()
 * @method getZipCode()
 * @method getPhone()
 * @method getCellPhone()
 * @method getDob()
 * @method getPrivacyAccepted()
 * @method getGender()
 * @method getSubscriberEmail()
 *
 * @method Contactlab_Subscribers_Model_Fields setNotes($value)
 * @method Contactlab_Subscribers_Model_Fields setFirstName($value)
 * @method Contactlab_Subscribers_Model_Fields setLastName($value)
 * @method Contactlab_Subscribers_Model_Fields setCustom1($value)
 * @method Contactlab_Subscribers_Model_Fields setCustom2($value)
 * @method Contactlab_Subscribers_Model_Fields setZipCode($value)
 * @method Contactlab_Subscribers_Model_Fields setPhone($value)
 * @method Contactlab_Subscribers_Model_Fields setCellPhone($value)
 * @method Contactlab_Subscribers_Model_Fields setDob($value)
 * @method Contactlab_Subscribers_Model_Fields setPrivacyAccepted($value)
 * @method Contactlab_Subscribers_Model_Fields setGender($value)
 * @method Contactlab_Subscribers_Model_Fields setSubscriberEmail($value)
 */
class Contactlab_Subscribers_Model_Fields extends Mage_Core_Model_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/fields");
    }

}
