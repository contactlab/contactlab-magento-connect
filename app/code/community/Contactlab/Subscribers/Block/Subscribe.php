<?php


/**
 * Newsletter subscribe block
 *
 * @category   Contactlab
 * @package    Contactlab_Newsletter
 * @author     
 */

class Contactlab_Subscribers_Block_Subscribe extends Mage_Newsletter_Block_Subscribe
{
    protected $fields = array(
        'email' => array('label' => "Your email" , 'type' => 'text', 'title' => 'Sign up for our newsletter'),
        'fname' => array('label' => "First name" , 'type' => 'text', 'title' => 'Your first name'),
        'lname' => array('label' => "Last name" , 'type' => 'text', 'title' => 'Your last name'),
        'company' => array('label' => "Your company" , 'type' => 'text', 'title' => 'Your company'),
        'gender' => array('label' => "Sex" , 'type' => 'radio', 'choices' => array('Male', 'Female')),
        'dob' => array('label' => "Date of birth" , 'type' => 'text'),
        'privacy' => array('label' => "Privacy terms agreement" , 'type' => 'checkbox'),
        'custom1' => array('label' => "No label assigned" , 'type' => 'text'),
        'custom2' => array('label' => "No label assigned" , 'type' => 'text'),
        'country' => array('label' => "Your country" , 'type' => 'text'),
        'city' => array('label' => "Your city" , 'type' => 'text'),
        'address' => array('label' => "Your address" , 'type' => 'text'),
        'zipcode' => array('label' => "Your ZIP code" , 'type' => 'text'),
        'landphone' => array('label' => "Your landline phone number" , 'type' => 'text'),
        'mobilephone' => array('label' => "Your mobile phone number" , 'type' => 'text'),
        'notes' => array('label' => "Additional notes" , 'type' => 'text')
    );
    
    public function __construct(){
      //get custom field labels
    }
    public function isEnabledField($fieldname){
        return array_key_exists($fieldname,$this->fields) ?  
            array_key_exists('enabled',$this->fields[$fieldname]) ?
              $this->fields[$fieldname]['enabled']  
              : true
            : false;
    }
    public function fieldLabel($fieldname){
        return array_key_exists($fieldname,$this->fields) ? $this->__($this->fields[$fieldname]['label']) : NULL;
    }

    public function fieldInputType($fieldname){
        return array_key_exists($fieldname,$this->fields) ? $this->fields[$fieldname]['type'] : 'text' ;
    }
    
    public function fieldChoices($fieldname){
        if(array_key_exists($fieldname,$this->fields))
           return $this->fields[$fieldname]['type'] === 'radio' ? 
                    count($this->fields[$fieldname]['choices'])
                    : 0;
        else return 0;
    }

    public function fieldChoice($fieldname,$choicenum){
        if(array_key_exists($fieldname,$this->fields))
           return $this->fields[$fieldname]['type'] === 'radio' ? 
                    $this->__($this->fields[$fieldname]['choices'][$choicenum-1])
                    : NULL;
        else return NULL;
    }
    
    public function fieldValue($fieldname,$choicenum){
        return $choicenum;
        if(array_key_exists($fieldname,$this->fields))
           return $this->fields[$fieldname]['type'] === 'radio' ? 
                    $this->fields[$fieldname]['choices'][$choicenum-1]
                    : NULL;
        else return NULL;
    }
    
    public function fieldTitle($fieldname) {
        if(array_key_exists($fieldname,$this->fields))
           return array_key_exists('title',$this->fields[$fieldname]) ? 
                    $this->__($this->fields[$fieldname]['title'])
                    : NULL;
        else return NULL;
    }
}



