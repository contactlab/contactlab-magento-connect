<?php

/**
 * Contactlab template model newsletter processor filter abstract distinct by type.
 *
 * @abstract
 */
abstract class Contactlab_Template_Model_Newsletter_Processor_Filter_AbstractDistinctByType
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {
    /**
     * Apply filter.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $queueLink = $rs->getTablename('newsletter/queue_link');
        $queue = $rs->getTablename('newsletter/queue');
        $template = $rs->getTablename('newsletter/template');
        $templateType = $rs->getTablename('contactlab_template/type');

        $field1 = $this->isCustomerCollection($collection) ? 'customer_id' : 'subscriber_id';
        $field2 = $this->isCustomerCollection($collection) ? 'entity_id' : 'subscriber_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        $collection->getSelect()->where(" not exists (select *
                                            from $queue
                                            join $queueLink on $queueLink.queue_id = $queue.queue_id
                                            join $template on $template.template_id = $queue.template_id
                                            join $templateType on $templateType.entity_id = $template.template_type_id
                                           where $queue.queue_status in ("
                                             . Mage_Newsletter_Model_Queue::STATUS_NEVER
                                             . ", " . Mage_Newsletter_Model_Queue::STATUS_SENDING . ") and
                                                 $templateType.template_type_code = '" . $this->getTemplateTypeCode() . "' and
                                                 $queueLink.$field1 = $mainTable.$field2)");
        return $collection;
    }

    /**
     * Wishlist or cart?
     */
    protected abstract function getTemplateTypeCode();
}
