<?php

class Contactlab_Template_Model_Resource_Newsletter_Queue_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('contactlab_template/newsletter_queue_link');
    }

    public function addCustomerInfo()
    {
        $this->getSelect()->joinLeft(
            array('customer' => $this->getTable('customer/entity')),
            'main_table.customer_id = customer.entity_id',
            array('customer_email' => 'email'));
        return $this;
    }

    public function addSubscriberInfo()
    {
        $this->getSelect()->joinLeft(
            array('newsletter_subscriber' => $this->getTable('newsletter/subscriber')),
            'main_table.subscriber_id = newsletter_subscriber.subscriber_id',
            array('subscriber_email' => 'subscriber_email'));
        return $this;
    }
}