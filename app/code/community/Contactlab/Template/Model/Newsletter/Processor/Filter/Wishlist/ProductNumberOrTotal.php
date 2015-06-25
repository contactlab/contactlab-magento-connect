<?php

/** Filter wishlist product number OR total. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Wishlist_ProductNumberOrTotal
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter to include only customers that have a product number > x into wishlist or
     * a total value into the range.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        $rs = $resource = Mage::getSingleton('core/resource');
        $wishlist = $rs->getTablename('wishlist/wishlist');
        $wishlistItem = $rs->getTablename('wishlist/item');
        $entityType = $rs->getTablename('eav/entity_type');
        $attribute = $rs->getTablename('eav/attribute');
        $catalog = $rs->getTablename('catalog/product') . "_decimal";
        $storeId = $this->getStoreId();

        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';

        if (!$this->isEmtpy($parameters['min']) && !$this->isEmtpy($parameters['max'])) {
            $condition = "(sum($wishlistItem.qty * $catalog.value) between " . $parameters['min'] . ' and ' . $parameters['max'] . ")";
        } else if (!$this->isEmtpy($parameters['max'])) {
            $condition = "(sum($wishlistItem.qty * $catalog.value) <= " . $parameters['max'] . ")";
        } else if (!$this->isEmtpy($parameters['min'])) {
            $condition = "(sum($wishlistItem.qty * $catalog.value) >= " . $parameters['min'] . ")";
        } else {
            return;
        }
        if ($this->isEmtpy($parameters['minnr']) && $this->isEmtpy($parameters['maxnr'])) {
            $condition2 = "1 = 1";
        } else {
            $w = $this->getOffsetWhereQty($parameters, 'minnr', 'maxnr');
            $condition2 = "(sum($wishlistItem.qty) $w)";
        }
        $collection->getSelect()->where("exists (select $wishlist.customer_id
          from $wishlist
	      join $entityType on $entityType.entity_type_code = 'catalog_product'
	      join $attribute on $attribute.entity_type_id = $entityType.entity_type_id and $attribute.attribute_code = 'price'
		  join $wishlistItem on $wishlistItem.wishlist_id = $wishlist.wishlist_id and
                                $wishlistItem.store_id = $storeId
          join $catalog on $catalog.entity_id = $wishlistItem.product_id and $catalog.attribute_id = eav_attribute.attribute_id
         where $wishlist.customer_id = $mainTable.$field
         group by $wishlist.customer_id
        having $condition or $condition2)");

        return $collection;
    }
    
    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter wishlist by product number and total";
    }
}
