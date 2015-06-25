<?php

/** Filter wishlist product number. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Wishlist_ProductNumber
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter to include only customers that have a product number > x into wishlist.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $wishlist = $rs->getTablename('wishlist/wishlist');
        $wishlistItem = $rs->getTablename('wishlist/item');
        $storeId = $this->getStoreId();

        $w = $this->getOffsetWhereQty($parameters);
        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        $collection->getSelect()->where("exists (select $wishlist.customer_id
          from $wishlist
		  join $wishlistItem on $wishlistItem.wishlist_id = $wishlist.wishlist_id and
                                $wishlistItem.store_id = $storeId
         where $wishlist.customer_id = $mainTable.$field
         group by $wishlist.customer_id
         having sum($wishlistItem.qty) $w)");

        return $collection;
    }
    
    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter wishlist by product number";
    }
}
