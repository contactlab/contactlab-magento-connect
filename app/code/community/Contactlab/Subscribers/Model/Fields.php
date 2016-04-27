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

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {
        $rv = parent::load($id, $field);
        if (!$this->hasData('subscriber_id')) {
            if ($field === 'subscriber_id') {
                $this->createFromSubscriber($id);
            } else if ($field === 'subscriber_email') {
                $this->createFromSubscriberEmail($id);
            }
        }

        return $rv;
    }

    /**
     * @param $id
     * @throws Exception
     */
    private function createFromSubscriber($id) {
        $subscriber = Mage::getModel('newsletter/subscriber')->load($id);
        $this->setSubscriberId($id);
        $this->setSubscriberEmail($subscriber->getEmail());
        $this->save();
    }

    /**
     * @param $email
     * @throws Exception
     */
    private function createFromSubscriberEmail($email) {
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
        $this->setSubscriberId($subscriber->getSubscriberId());
        $this->setSubscriberEmail($subscriber->getEmail());
        $this->save();
    }
}
