<?php

/** Filter cart product number OR total. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Cart_ProductNumberOrTotal
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter to include only customers that have a product number > x into cart or
     * a total value into the range.
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
            $condition = "$cart.grand_total between " . $parameters['min'] . ' and ' . $parameters['max'];
        } else if (!$this->isEmtpy($parameters['max'])) {
            $condition = "$cart.grand_total <= " . $parameters['max'];
        } else if (!$this->isEmtpy($parameters['min'])) {
            $condition = "$cart.grand_total >= " . $parameters['min'];
        } else {
            return;
        }
        if ($this->isEmtpy($parameters['minnr']) && $this->isEmtpy($parameters['maxnr'])) {
            $condition2 = "1 = 1";
        } else {
            $w = $this->getOffsetWhereQty($parameters, 'minnr', 'maxnr');
            $condition2 = "$cart.items_qty $w";
        }
        $collection->getSelect()->where("exists (select *
          from $cart
         where $cart.customer_id = $mainTable.$field and
               $cart.store_id = $storeId and
               ($condition or $condition2))");

        return $collection;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter cart by product number and total";
    }

}
