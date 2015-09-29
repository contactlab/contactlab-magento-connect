<?php

/**
 * Render for store column.
 */
class Contactlab_Commons_Block_Adminhtml_Tasks_Renderer_Store
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        if ($row->getStoreId() === null) {
            return '';
        }
    	return Mage::app()->getStore($row->getStoreId())->getName();
    }
}
