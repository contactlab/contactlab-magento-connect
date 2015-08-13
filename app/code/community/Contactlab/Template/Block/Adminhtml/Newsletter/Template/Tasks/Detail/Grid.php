<?php

/** Adminhtml type grid. */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_Detail_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the block.
     *
     * @param array $attributes = array()
     */
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('queue_link_id');
        $this->setDefaultSort('queue_link_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
    }

    /**
     * Setting collection to show.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        /* @var $queueCollection Contactlab_Template_Model_Resource_Newsletter_Queue_Link_Collection */
        $queueCollection = Mage::getResourceModel("contactlab_template/newsletter_queue_link_collection");
        $queueCollection->addFieldToFilter('queue_id', Mage::registry('queue_id'));
        $queueCollection->addCustomerInfo();
        $queueCollection->addSubscriberInfo();
        $this->setCollection($queueCollection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('queued_at', array(
            'header' => $this->__('Queued'),
            'align' => 'left',
            'index' => 'queued_at',
            'width' => 1,
            'type' => 'range'
        ));
        $this->addColumn('subscriber_email', array(
            'header' => Mage::helper('newsletter')->__('Subscriber'),
            'align' => 'left',
            'index' => 'subscriber_email',
            'width' => 1,
            'filter' => false,
            'order' => false,
            'type' => 'range'
        ));
        $this->addColumn('customer_email', array(
            'header' => Mage::helper('customer')->__('Customer'),
            'align' => 'left',
            'index' => 'customer_email',
            'width' => 1,
            'filter' => false,
            'order' => false,
            'type' => 'range',
            'renderer' => 'contactlab_template/adminhtml_newsletter_template_tasks_renderer_customer'
        ));
    }

    /**
     * Get grid url.
     *
     * @return string
     */
    public function getGridUrl() {
        return false;
    }

    /**
     * Get row url.
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row) {
        return false;
    }
}
