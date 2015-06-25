<?php

/** Disntinct By Type Filter . */
class Contactlab_Template_Model_Newsletter_Processor_Filter_Cart_DistinctByType
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_AbstractDistinctByType {
    /**
     * Wishlist or cart?
     */
    protected function getTemplateTypeCode() {
        return "CART";
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter only addresses that has not a mail in queue with the same template type (Abandoned Cart)";
    }
}
