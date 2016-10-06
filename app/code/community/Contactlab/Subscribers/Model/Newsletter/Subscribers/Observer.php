<?php

/**
 * Add custom fields to subscribers grid.
 * Class Contactlab_Subscribers_Model_Newsletter_Subscribers_Observer
 */
class Contactlab_Subscribers_Model_Newsletter_Subscribers_Observer extends Mage_Core_Model_Abstract
{

    /**
     * Remove old columns.
     * @param Varien_Event_Observer $event
     * @return $this
     */
    public function removeOldColumns(Varien_Event_Observer $event)
    {
        /* @var $block Mage_Adminhtml_Block_Newsletter_Subscriber_Grid */
        $block = $event->getBlock();
        if (!isset($block)) {
            return $this;
        }
				
		if ($block instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {
			$this->_removeColumnsToGrid($block);
		}
		
        return $this;
    }
    
    
    /**
     * Append custom columns.
     * @param Varien_Event_Observer $event
     * @return $this
     */
    public function appendCustomColumns(Varien_Event_Observer $event)
    {
    	/* @var $block Mage_Adminhtml_Block_Newsletter_Subscriber_Grid */
    	$block = $event->getBlock();
    	if (!isset($block)) {
    		return $this;
    	}
    
    	if ($block instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {
    		$this->_addColumnsToGrid($block);
    	}
    
    	return $this;
    }
    
    /**
     * Remove columns to grid.
     * @param Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block
     * @return Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
     */
    private function _removeColumnsToGrid(Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block)
    {        
    	$block->removeColumn('email')
    		->removeColumn('firstname')
    		->removeColumn('lastname')
    		->sortColumnsByOrder();
    	return $block;
    }
    

    /**
     * Add columns to grid.
     * @param Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block
     * @return Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
     */
    private function _addColumnsToGrid(Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block)
    {    	 
    	$block
    		->addColumnAfter('subscriber_email', array(
    			'header' => Mage::helper('newsletter')->__('Email C'),
    			'index' => 'subscriber_email',
    			'filter_index' => 'main_table.subscriber_email',
    			'default' => '---'), 'subscriber_id')
            ->addColumnAfter('fname', array(
                'header' => Mage::helper('newsletter')->__('First Name'),
                'index' => 'fname2',
                'default' => '---'), 'type')
            ->addColumnAfter('lname', array(
                'header' => Mage::helper('newsletter')->__('Last Name'),
                'index' => 'lname2',
                'default' => '---'), 'fname')
            ->addColumnAfter('dob2', array(
                'header' => Mage::helper('newsletter')->__('Date of Birth'),
                'index' => 'dob2',
                'type' => 'date',
                'align' => 'center',
                'default' => '---'), 'lname')
            ->addColumnAfter('gender2', array(
                'header' => Mage::helper('newsletter')->__('Gender'),
                'index' => 'gender2',
                'type' => 'options',
                'options' => array(
                    '',
                    Mage::helper('contactlab_subscribers')->__('M'),
                    Mage::helper('contactlab_subscribers')->__('F')),
                'default' => '---'), 'dob2')
            ->addColumnAfter('country2', array(
                'header' => Mage::helper('newsletter')->__('Country'),
                'index' => 'country2',
                'default' => '---'), 'gender2')
            ->addColumnAfter('city2', array(
                'header' => Mage::helper('newsletter')->__('City'),
                'index' => 'city2',
                'default' => '---'), 'country2')
            ->addColumnAfter('zipcode2', array(
                'header' => Mage::helper('newsletter')->__('Zip Code'),
                'index' => 'zipcode2',
                'default' => '---'), 'city2')
            ->addColumnAfter('address2', array(
                'header' => Mage::helper('newsletter')->__('Address'),
                'index' => 'address2',
                'default' => '---'), 'zipcode2')
            ->addColumnAfter('phone2', array(
                'header' => Mage::helper('newsletter')->__('Landline Phone'),
                'index' => 'phone2',
                'default' => '---'), 'address2')
            ->addColumnAfter('mphone', array(
                'header' => Mage::helper('newsletter')->__('Mobile Phone'),
                'index' => 'mphone',
                'default' => '---'), 'phone2')
            ->addColumnAfter('company', array(
                'header' => Mage::helper('newsletter')->__('Company'),
                'index' => 'company',
                'default' => '---'), 'mphone')
            ->addColumnAfter('privacy', array(
                'header' => Mage::helper('newsletter')->__('Privacy Terms Agreement'),
                'index' => 'privacy',
                'type' => 'options',
                'options' => array('no', 'si')), 'company')
            ->addColumnAfter('custom_1', array(
                'header' => Mage::helper('newsletter')->__('Custom info 1'),
                'index' => 'custom_1',
                'default' => '---'), 'privacy')
            ->addColumnAfter('custom_2', array(
                'header' => Mage::helper('newsletter')->__('Custom info 2'),
                'index' => 'custom_2',
                'default' => '---'), 'custom_1')
            ->addColumnAfter('notes', array(
                'header' => Mage::helper('newsletter')->__('Notes'),
                'index' => 'notes',
                'width' => '100'), 'custom_2')
            ;        	
            
        return $block;
    }

    public function appendCustomAttributes(Varien_Event_Observer $event)
    {
        if ($this->_isGridAction() && $this->_isNewsletterSubscriberCollection($event)) {
            $this->_addCustomAttributes($event->getCollection());
        }
    }

    private function _isGridAction()
    {
        $request = Mage::app()->getRequest();
        if ($request->getModuleName() !== 'admin') {
            return false;
        }
        if ($request->getControllerName() !== 'newsletter_subscriber') {
            return false;
        }
        return $request->getActionName() === 'index' ||$request->getActionName() === 'grid';
    }

    private function _isNewsletterSubscriberCollection($event)
    {
        return $event->hasCollection() && ($event->getCollection() instanceof Mage_Newsletter_Model_Resource_Subscriber_Collection);
    }

    private function _addCustomAttributes($collection)
    {
    	$tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $collection->getSelect()
            ->joinLeft(array('csnsf' => $tablePrefix.'contactlab_subscribers_newsletter_subscriber_fields'),
                'csnsf.subscriber_id = main_table.subscriber_id',
                array(
                    'privacy' => 'csnsf.privacy_accepted',
                    'company' => 'csnsf.company',
                    'fname2' => 'csnsf.first_name',
                    'lname2' => 'csnsf.last_name',
                    'gender2' => 'csnsf.gender',
                    'dob2' => 'csnsf.dob',
                    'custom_1' => 'csnsf.custom_1',
                    'custom_2' => 'csnsf.custom_2',
                    'notes' => 'csnsf.notes',
                    'country2' => 'csnsf.country',
                    'address2' => 'csnsf.address',
                    'zipcode2' => 'csnsf.zip_code',
                    'phone2' => 'csnsf.phone',
                    'mphone' => 'csnsf.cell_phone',
                	'email' => 'csnsf.subscriber_email',	
                    'city2' => 'csnsf.city'));                
    }
    
}