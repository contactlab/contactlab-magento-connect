<?php

/** Add products ids. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Wishlist_Products
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
        $wishlist = $rs->getTablename('wishlist/wishlist');
        $wishlistItem = $rs->getTablename('wishlist/item');
        $storeId = $this->getStoreId();

        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        $w = "$wishlist.customer_id = $mainTable.$field";

        $collection->getSelect()->join(array('wishlist' => $wishlist), $w, array());

        $collection->getSelect()->join(
            array('wishlist_items' => $wishlistItem),
                    "wishlist_items.wishlist_id = wishlist.wishlist_id and wishlist_items.store_id = $storeId",
                        array('product_ids' => 'GROUP_CONCAT(concat(product_id, \'|\', qty, \'|\', wishlist_item_id)'
                            . ' ORDER BY qty desc SEPARATOR \',\')'));

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
        return "Get products in wishlist";
    }
}
