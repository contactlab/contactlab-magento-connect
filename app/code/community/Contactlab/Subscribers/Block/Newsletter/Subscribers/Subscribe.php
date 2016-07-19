<?php


/**
 * Newsletter subscribe block
 *
 * @category   Contactlab
 * @package    Contactlab_Newsletter
 * @author
 */
class Contactlab_Subscribers_Block_Newsletter_Subscribers_Subscribe extends Mage_Newsletter_Block_Subscribe
{
	
	protected function _toHtml()
	{
		if(Mage::getStoreConfig('contactlab_subscribers/newsletter/enable'))
		{
			return parent::_toHtml();
		}
	}
	
    /**
     * Fields.
     * @var array
     */
    protected $fields = array(
        'email' => array('label' => "Your email", 'type' => 'text', 'title' => 'Sign up for our newsletter'),
        'fname' => array('label' => "First name", 'type' => 'text', 'title' => 'Your first name'),
        'lname' => array('label' => "Last name", 'type' => 'text', 'title' => 'Your last name'),
        'company' => array('label' => "Your company", 'type' => 'text', 'title' => 'Your company'),
        'gender' => array('label' => "Sex", 'type' => 'radio', 'choices' => array('Male', 'Female')),
        'dob' => array('label' => "Date of birth", 'type' => 'text'),
        'privacy' => array('label' => "Privacy terms agreement", 'type' => 'checkbox'),
        'custom1' => array('label' => "No label assigned", 'type' => 'text'),
        'custom2' => array('label' => "No label assigned", 'type' => 'text'),
        'country' => array('label' => "Your country", 'type' => 'text'),
        'city' => array('label' => "Your city", 'type' => 'text'),
        'address' => array('label' => "Your address", 'type' => 'text'),
        'zipcode' => array('label' => "Your ZIP code", 'type' => 'text'),
        'landphone' => array('label' => "Your landline phone number", 'type' => 'text'),
        'mobilephone' => array('label' => "Your mobile phone number", 'type' => 'text'),
        'notes' => array('label' => "Additional notes", 'type' => 'text')
    );

    /**
     * Is enabled field.
     * @param $fieldName
     * @return bool
     */
    public function isEnabledField($fieldName)
    {
        return array_key_exists($fieldName, $this->fields) ?
            array_key_exists('enabled', $this->fields[$fieldName]) ?
                $this->fields[$fieldName]['enabled']
                : true
            : false;
    }

    /**
     * Field label.
     * @param $fieldName
     * @return null|string
     */
    public function fieldLabel($fieldName)
    {
        return array_key_exists($fieldName, $this->fields) ? $this->__($this->fields[$fieldName]['label']) : NULL;
    }

    /**
     * Field input type.
     * @param $fieldName
     * @return string
     */
    public function fieldInputType($fieldName)
    {
        return array_key_exists($fieldName, $this->fields) ? $this->fields[$fieldName]['type'] : 'text';
    }

    /**
     * Field choices.
     * @param $fieldName
     * @return int
     */
    public function fieldChoices($fieldName)
    {
        if (array_key_exists($fieldName, $this->fields))
            return $this->fields[$fieldName]['type'] === 'radio' ?
                count($this->fields[$fieldName]['choices'])
                : 0;
        else return 0;
    }

    /**
     * Field choice.
     * @param $fieldName
     * @param $choiceNum
     * @return null|string
     */
    public function fieldChoice($fieldName, $choiceNum)
    {
        if (array_key_exists($fieldName, $this->fields)) {
            return $this->fields[$fieldName]['type'] === 'radio' ?
                $this->__($this->fields[$fieldName]['choices'][$choiceNum - 1])
                : null;
        } else {
            return null;
        }
    }

    /**
     * Field value.
     * @param $fieldName
     * @param $choiceNum
     * @return mixed
     */
    public function fieldValue($fieldName, $choiceNum)
    {
        return $choiceNum;
        /*if (array_key_exists($fieldname, $this->fields)) {
            return $this->fields[$fieldname]['type'] === 'radio' ?
                $this->fields[$fieldname]['choices'][$choicenum - 1]
                : null;
        } else {
            return null;
        }*/
    }

    /**
     * Field title.
     * @param $fieldName
     * @return null|string
     */
    public function fieldTitle($fieldName)
    {
        if (array_key_exists($fieldName, $this->fields)) {
            return array_key_exists('title', $this->fields[$fieldName]) ?
                $this->__($this->fields[$fieldName]['title'])
                : null;
        } else {
            return null;
        }
    }
}
