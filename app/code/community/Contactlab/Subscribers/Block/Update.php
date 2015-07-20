<?php

/**
 * Newsletter update block
 *
 * @category   Contactlab
 * @package    Contactlab_Newsletter
 * @author     
 */

class Contactlab_Subscribers_Block_Update extends Contactlab_Subscribers_Block_Subscribe
{
    private $_fieldvalues;
    public function __construct() {
        //get registered fields value
        $this->_fieldvalues = Mage::registry('contactlab/fields');
        if(is_null($this->_fieldvalues))
            Mage::throwException($this->__('Update block instantiated out of context'));
    }
    /**
     *  depending on type of field, builds value or checked attribute for
     *   the field, getting its value from registered fields
     * 
     * @param string $fieldname
     */
    public function fieldActualValue($fieldname,$choicenum = 0) {
        if($fieldname == 'notes') return $this->_fieldvalues->getNotes();
        
        if($fieldname == 'fname') return ' value="' . $this->_fieldvalues->getFirstName() . '"';
        if($fieldname == 'lname') return ' value="' . $this->_fieldvalues->getLastName() . '"';
        if($fieldname == 'custom1') return ' value="' . $this->_fieldvalues->getCustom1() . '"';
        if($fieldname == 'custom2') return ' value="' . $this->_fieldvalues->getCustom2() . '"';
        if($fieldname == 'zipcode') return ' value="' . $this->_fieldvalues->getZipCode() . '"';
        if($fieldname == 'landphone') return ' value="' . $this->_fieldvalues->getPhone() . '"';
        if($fieldname == 'mobilephone') return ' value="' . $this->_fieldvalues->getCellPhone() . '"';

        if($fieldname == 'dob'){
            $dateformat = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $ts = date_timestamp_get(date_create($this->_fieldvalues->getDob()));
            $dob = strftime($dateformat, $ts);
            return ' value="' . $dob . '"';
        } 

        if($this->fieldInputType($fieldname) == 'text')
            return ' value="' . $this->_fieldvalues->getData($fieldname) . '" ';
        
        if(($fieldname == 'privacy') && ($this->_fieldvalues->getPrivacyAccepted()))
            return ' checked="yes" ';
        
        if(($fieldname == 'gender') && ($this->_fieldvalues->getGender() == $choicenum))
            return ' checked="yes" ';

        return '';
    }
    
    public function getSubsEmail() {
        return $this->_fieldvalues->getSubscriberEmail();
    }
    
    public function getModifyFormActionUrl()
    {
        return $this->getUrl('contactlab/modify/personal', array('_secure' => true));
    }
    
}

