<?php

/** Filter cart datetime offset. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Cart_Offset
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter for datetime offset (cart older than x minutes and no queued mail younger that cart).
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $cart = $rs->getTablename('sales/quote');
        $queueLink = $rs->getTablename('newsletter/queue_link');
        $queue = $rs->getTablename('newsletter/queue');
        $template = $rs->getTablename('newsletter/template');
        $templateType = $rs->getTablename('contactlab_template/type');
        $storeId = $this->getStoreId();
        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $field1 = $this->isCustomerCollection($collection) ? 'customer_id' : 'subscriber_id';
        $field2 = $this->isCustomerCollection($collection) ? 'entity_id' : 'subscriber_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        $offsetWhere = $this->getOffsetWhere($parameters);

        // TODO Should we delete older queued email with the same template?
        // FIXME queue_link_store?
        $collection->getSelect()->where("exists (select *
          from $cart
         where $cart.customer_id = $mainTable.$field and
               $cart.store_id = $storeId and
               TIME_TO_SEC(timediff(utc_timestamp(), $cart.updated_at)) $offsetWhere and
               not exists (select *
                             from $queue
                             join $template on $template.template_id = $queue.template_id
                             join $templateType on $templateType.entity_id = $template.template_type_id and
                                                   $templateType.template_type_code = 'CART'
                             join $queueLink on $queueLink.queue_id = $queue.queue_id
                            where $queue.queue_status != 2 and
                                  $queueLink.$field1 = $mainTable.$field2 and
                                  $queueLink.queued_at > $cart.updated_at))");

        return $collection;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Apply time related filters (Abandoned Cart)";
    }

}
