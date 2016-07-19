<?php

/**
 * Newsletter update block
 *
 * @category   Contactlab
 * @package    Contactlab_Newsletter
 * @author
 */
class Contactlab_Subscribers_Block_Newsletter_Subscribers_Update extends Contactlab_Subscribers_Block_Newsletter_Subscribers_Subscribe
{
    /**
     * @var $_fieldValues Contactlab_Subscribers_Model_Fields
     */
    private $_fieldValues;

    /**
     * Construct.
     * @throws Mage_Core_Exception
     */
    public function _construct()
    {
        parent::_construct();
        //get registered fields value
        $this->_fieldValues = Mage::registry('contactlab/fields');
        if (is_null($this->_fieldValues)) {
            Mage::throwException($this->__('Update block instantiated out of context'));
        }
    }

    /**
     * Depending on type of field, builds value or checked attribute for
     * the field, getting its value from registered fields
     *
     * @param string $fieldName
     * @param int $choiceNum
     * @return string
     */
    public function fieldActualValue($fieldName, $choiceNum = 0)
    {
        if ($fieldName == 'notes') {
            return $this->_fieldValues->getNotes();
        }

        if ($fieldName == 'fname') {
            return ' value="' . $this->_fieldValues->getFirstName() . '"';
        }
        if ($fieldName == 'lname') {
            return ' value="' . $this->_fieldValues->getLastName() . '"';
        }
        if ($fieldName == 'custom1') {
            return ' value="' . $this->_fieldValues->getCustom1() . '"';
        }
        if ($fieldName == 'custom2') {
            return ' value="' . $this->_fieldValues->getCustom2() . '"';
        }
        if ($fieldName == 'zipcode') {
            return ' value="' . $this->_fieldValues->getZipCode() . '"';
        }
        if ($fieldName == 'landphone') {
            return ' value="' . $this->_fieldValues->getPhone() . '"';
        }
        if ($fieldName == 'mobilephone') {
            return ' value="' . $this->_fieldValues->getCellPhone() . '"';
        }

        if ($fieldName == 'dob') {
            $dateFormat = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $ts = date_timestamp_get(date_create($this->_fieldValues->getDob()));
            $dob = strftime($dateFormat, $ts);
            return ' value="' . $dob . '"';
        }

        if ($this->fieldInputType($fieldName) == 'text') {
            return ' value="' . $this->_fieldValues->getData($fieldName) . '" ';
        }

        if (($fieldName == 'privacy') && ($this->_fieldValues->getPrivacyAccepted())) {
            return ' checked="yes" ';
        }

        if (($fieldName == 'gender') && ($this->_fieldValues->getGender() == $choiceNum)) {
            return ' checked="yes" ';
        }

        return '';
    }

    /**
     * Get subs email.
     * @return mixed
     */
    public function getSubsEmail()
    {
        return $this->_fieldValues->getSubscriberEmail();
    }

    /**
     * Get subs id.
     * @return mixed
     */
    public function getSubsId()
    {
        return $this->_fieldValues->getSubscriberId();
    }

    /**
     * Get subs hash.
     * @return mixed
     */
    public function getSubsHash()
    {
        return $this->_fieldValues->getSubscriberConfirmCode();
    }

    /**
     * Modify form action url.
     * @return string
     */
    public function getModifyFormActionUrl()
    {
        return $this->getUrl('contactlab_subscribers/modify/personal', array('_secure' => true));
    }

}
