<?php

/** Filter customer that have a cart. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Cart_Have
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter to include only customers that have a cart.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return $collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $cart = $rs->getTablename('sales/quote');
        $storeId = $this->getStoreId();
        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        $collection->getSelect()->where("exists (select *
          from $cart
         where $cart.customer_id = $mainTable.$field and
               $cart.is_active = 1 and
               $cart.is_virtual = 0 and
               $cart.items_count > 0 and
               $cart.store_id = $storeId)");

        return $collection;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter customers with items in cart";
    }
}
