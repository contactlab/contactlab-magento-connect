<?php

/**
 * Generic processor
 */
class Contactlab_Template_Model_Newsletter_Processor_Generic extends Contactlab_Template_Model_Newsletter_Processor_Abstract {
    /**
     * Apply subscribers filter.
     *
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @return $this
     */
    public function applySubscribersFilter(Contactlab_Template_Model_Newsletter_Template $template) {
        // FIXME is it right?
        $this->applyFilter('contactlab_template/newsletter_processor_filter_onlyCustomers');
        $this->applyFilter('contactlab_template/newsletter_processor_filter_store',
                array('store_id' => $this->getStoreId()));

        return $this;
    }

    /**
     * Get processor code.
     *
     * @return string
     */
    public function getProcessorCode() {
        return 'generic';
    }

    /**
     * Get price for product.
     * @param Mage_Catalog_Model_Product $product
     * @param string[] $item
     * @return string
     */
    public function getPriceFor(Mage_Catalog_Model_Product $product, array $item) {
        return "";
    }
}
