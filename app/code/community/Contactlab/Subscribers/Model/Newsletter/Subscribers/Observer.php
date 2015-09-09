<?php

/**
 * Add custom fields to subscribers grid.
 * Class Contactlab_Subscribers_Model_Newsletter_Subscribers_Observer
 */
class Contactlab_Subscribers_Model_Newsletter_Subscribers_Observer extends Mage_Core_Model_Abstract
{

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

        if ($block->getType() == 'adminhtml/newsletter_subscriber_grid') {
            $this->_addColumnsToGrid($block);
        }
        return $this;
    }

    /**
     * Add columns to grid.
     * @param Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block
     * @return Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
     */
    private function _addColumnsToGrid(Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block)
    {
        $block
            ->addColumnAfter('fname', array(
                'header' => Mage::helper('newsletter')->__('First name'),
                'index' => 'fname2',
                'default' => '---'), 'type')
            ->addColumnAfter('lname', array(
                'header' => Mage::helper('newsletter')->__('Last name'),
                'index' => 'lname2',
                'default' => '---'), 'fname')
            ->addColumnAfter('dob2', array(
                'header' => Mage::helper('newsletter')->__('Date of birth'),
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
                'header' => Mage::helper('newsletter')->__('Landline phone'),
                'index' => 'phone2',
                'default' => '---'), 'address2')
            ->addColumnAfter('mphone', array(
                'header' => Mage::helper('newsletter')->__('Mobile phone'),
                'index' => 'mphone',
                'default' => '---'), 'phone2')
            ->addColumnAfter('company', array(
                'header' => Mage::helper('newsletter')->__('Company'),
                'index' => 'company',
                'default' => '---'), 'mphone')
            ->addColumnAfter('privacy', array(
                'header' => Mage::helper('newsletter')->__('Privacy terms agreement'),
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
            ->removeColumn('firstname')->removeColumn('lastname')->sortColumnsByOrder();
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
        $collection->getSelect()
            ->joinLeft('contactlab_subscribers_newsletter_subscriber_fields',
                'contactlab_subscribers_newsletter_subscriber_fields.subscriber_id = main_table.subscriber_id',
                array(
                    'privacy'=>'privacy_accepted',
                    'company' => 'company',
                    'fname2' => 'first_name',
                    'lname2' => 'last_name',
                    'gender2' => 'gender',
                    'dob2' => 'dob',
                    'custom_1' => 'custom_1',
                    'custom_2' => 'custom_2',
                    'notes' => 'notes',
                    'country2' => 'country',
                    'address2' => 'address',
                    'zipcode2' => 'zip_code',
                    'phone2' => 'phone',
                    'mphone' => 'cell_phone',
                    'city2' => 'city'));
    }
}