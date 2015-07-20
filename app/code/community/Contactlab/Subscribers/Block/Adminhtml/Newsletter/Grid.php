<?php

class Contactlab_Subscribers_Block_Adminhtml_Newsletter_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
    public function __construct()
    {
        parent::__construct();
        Mage::helper('contactlab_commons')->logInfo(get_class($this));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
        /* @var $collection Mage_Newsletter_Model_Mysql4_Subscriber_Collection */
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

        $this->setCollection($collection);

        return parent::_prepareCollection();
        
    }
    
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn('company', array(
            'header'    => Mage::helper('newsletter')->__('Company'),
            'index'     => 'company',
            'default'   => '---'
        ))
        ->addColumn('privacy', array(
            'header'    => Mage::helper('newsletter')->__('Privacy terms agreement'),
            'index'     => 'privacy',
            'type'      => 'options',
            'options'   => array('no','si')
        ))
        ->addColumn('custom_1', array(
            'header'    => Mage::helper('newsletter')->__('Custom info 1'),
            'index'     => 'custom_1',
            'default'   => '---'
        ))
        ->addColumn('custom_2', array(
            'header'    => Mage::helper('newsletter')->__('Custom info 2'),
            'index'     => 'custom_2',
            'default'   => '---'
        ))
        ->addColumnAfter('fname', array(
            'header'    => Mage::helper('newsletter')->__('First name'),
            'index'     => 'fname2',
            'default'   => '---'
            ),
            'type')
        ->addColumnAfter('lname', array(
            'header'    => Mage::helper('newsletter')->__('Last name'),
            'index'     => 'lname2',
            'default'   => '---'
            ),
            'fname')
        ->addColumnAfter('dob2', array(
            'header'    => Mage::helper('newsletter')->__('Date of birth'),
            'index'     => 'dob2',
            'type'      => 'date',
            'align'     => 'center',
            'default'   => '---'
            ),
            'lname')
        ->addColumnAfter('gender2', array(
            'header'    => Mage::helper('newsletter')->__('Gender'),
            'index'     => 'gender2',
            'type'      => 'options',
            'options'   => array('',$this->__('M'),$this->__('F')),
            'default'   => '---'
            ),
            'dob2')
        ->addColumnAfter('country2', array(
            'header'    => Mage::helper('newsletter')->__('Country'),
            'index'     => 'country2',
            'default'   => '---'
            ),
            'gender2')
        ->addColumnAfter('city2', array(
            'header'    => Mage::helper('newsletter')->__('City'),
            'index'     => 'city2',
            'default'   => '---'
            ),
            'country2')
        ->addColumnAfter('zipcode2', array(
            'header'    => Mage::helper('newsletter')->__('Zip Code'),
            'index'     => 'zipcode2',
            'default'   => '---'
            ),
            'city2')
        ->addColumnAfter('address2', array(
            'header'    => Mage::helper('newsletter')->__('Address'),
            'index'     => 'address2',
            'default'   => '---'
            ),
            'zipcode2')
        ->addColumnAfter('phone2', array(
            'header'    => Mage::helper('newsletter')->__('Landline phone'),
            'index'     => 'phone2',
            'default'   => '---'
            ),
            'address2')
        ->addColumnAfter('mphone', array(
            'header'    => Mage::helper('newsletter')->__('Mobile phone'),
            'index'     => 'mphone',
            'default'   => '---'
            ),
            'phone2')
        ->addColumn('notes', array(
            'header'    => Mage::helper('newsletter')->__('Notes'),
            'index'     => 'notes',
            'width'     => '100',
        ))
            ->removeColumn('firstname')
            ->removeColumn('lastname')
            ->sortColumnsByOrder();
    }
}
