<?php

/** Add products ids. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Cart_Products
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Add products ids to select.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return $collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $cart = $rs->getTablename('sales/quote');
        $cartItem = $rs->getTablename('sales/quote_item');
        $storeId = $this->getStoreId();
        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        $w = "cart.customer_id = $mainTable.$field and
               cart.is_active = 1 and
               cart.is_virtual = 0 and
               cart.items_count > 0 and
               cart.store_id = $storeId";

        $collection->getSelect()->join(array('cart' => $cart), $w, array());

        $collection->getSelect()->join(
            array('cart_items' => $cartItem),
                    'cart_items.quote_id = cart.entity_id and cart_items.parent_item_id is null',
                        array('product_ids' => 'GROUP_CONCAT(concat(product_id, \'|\', qty, \'|\', item_id, \'|\', row_total)'
                            . ' ORDER BY row_total desc SEPARATOR \',\')'));

        $collection->getSelect()->group("$mainTable.$field");

        return $collection;
    }

    /**
     * Do run in test mode?
     *
     * @return false
     */
    public function doRunInTestMode() {
        return true;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Get products in abandoned cart";
    }
}
