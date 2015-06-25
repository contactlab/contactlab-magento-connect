<?php

/** Filter wishlist total value. */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Wishlist_TotalValue
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
        $wishlist = $rs->getTablename('wishlist/wishlist');
        $wishlistItem = $rs->getTablename('wishlist/item');
        $entityType = $rs->getTablename('eav/entity_type');
        $attribute = $rs->getTablename('eav/attribute');
        $catalog = $rs->getTablename('catalog/product') . "_decimal";
        $storeId = $this->getStoreId();

        if (!$this->isEmtpy($parameters['min']) && !$this->isEmtpy($parameters['max'])) {
            $condition = "between " . $parameters['min'] . ' and ' . $parameters['max'];
        } else if (!$this->isEmtpy($parameters['max'])) {
            $condition = "<= " . $parameters['max'];
        } else if (!$this->isEmtpy($parameters['min'])) {
            $condition = ">= " . $parameters['min'];
        } else {
            return;
        }
        $field = $this->isCustomerCollection($collection) ? 'entity_id' : 'customer_id';
        $mainTable = $this->isCustomerCollection($collection) ? 'e' : 'main_table';
        $collection->getSelect()->where("exists (select *
          from $wishlist
	      join $entityType on $entityType.entity_type_code = 'catalog_product'
	      join $attribute on $attribute.entity_type_id = $entityType.entity_type_id and $attribute.attribute_code = 'price'
		  join $wishlistItem on $wishlistItem.wishlist_id = $wishlist.wishlist_id and
                                $wishlistItem.store_id = $storeId
          join $catalog on $catalog.entity_id = $wishlistItem.product_id and $catalog.attribute_id = eav_attribute.attribute_id
         where $wishlist.customer_id = $mainTable.$field and
               $wishlistItem.qty * $catalog.value $condition)");

        return $collection;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter Wishlist by total value";
    }
}
