<?php

/** Filter cart total value. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Cart_TotalValue
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter to include only customers that have a total value into the range.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $cart = $rs->getTablename('sales/quote');
        $storeId = $this->getStoreId();
        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        if (!$this->isEmtpy($parameters['min']) && !$this->isEmtpy($parameters['max'])) {
            $condition = "between " . $parameters['min'] . ' and ' . $parameters['max'];
        } else if (!$this->isEmtpy($parameters['max'])) {
            $condition = "<= " . $parameters['max'];
        } else if (!$this->isEmtpy($parameters['min'])) {
            $condition = ">= " . $parameters['min'];
        } else {
            return;
        }
        $collection->getSelect()->where("exists (select *
          from $cart
         where $cart.customer_id = $mainTable.$field and
               $cart.store_id = $storeId and
               $cart.grand_total $condition)");

        return $collection;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter Abandoned Cart by total value";
    }
}
