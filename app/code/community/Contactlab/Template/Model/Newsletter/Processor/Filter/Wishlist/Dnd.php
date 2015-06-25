<?php

/** Do not disturb politic. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Wishlist_Dnd
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter for Do Not Disturb Politic.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $queueLink = $rs->getTablename('newsletter/queue_link');
        $queue = $rs->getTablename('newsletter/queue');

        $field1 = $this->isCustomerCollection($collection) ? 'customer_id' : 'subscriber_id';
        $field2 = $this->isCustomerCollection($collection) ? 'entity_id' : 'subscriber_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        // StoreId?
        $storeId = $this->getStoreId();

        // FIXME < or <= ?
        $collection->getSelect()->where("(select count(1)
                                            from $queue
                                            join $queueLink on $queueLink.queue_id = $queue.queue_id
                                           where $queue.queue_status != 2 and
                                                 $queueLink.$field1 = $mainTable.$field2 and
                                                 DATEDIFF(now(), $queueLink.queued_at) <= " . $parameters['period'] . ") < " . $parameters['mail_number']);

        // Mage::helper('contactlab_commons')->logInfo($collection->getSelect()->assemble());

        return $collection;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Apply Do Not Disturb policies";
    }
}
