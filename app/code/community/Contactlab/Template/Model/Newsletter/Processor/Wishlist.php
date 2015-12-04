<?php

/**
 * Wishlist processor.
 * Apply these filters:
 * - onlyCustomers
 * - have
 * - offset
 * and:
 * - productNumber
 * - totalValue
 *
 * or:
 * - productNumberOrTotal
 */
class Contactlab_Template_Model_Newsletter_Processor_Wishlist extends Contactlab_Template_Model_Newsletter_Processor_Abstract {
    /**
     * Apply subscribers filter.
     *
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @return $this
     */
    public function applySubscribersFilter(Contactlab_Template_Model_Newsletter_Template $template) {
        $this->applyFilter('contactlab_template/newsletter_processor_filter_onlyCustomers');
        $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_distinctByType');

        if ($template->getAndOr() == 'AND') {
            if ($template->hasMinProducts() || $template->hasMaxProducts()) {
                $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_productNumber',
                        array('min' => $template->getMinProducts(), 'max' => $template->getMaxProducts()));
            }
            if ($template->hasMinValue() || $template->hasMaxValue()) {
                $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_totalValue',
                        array('min' => $template->getMinValue(), 'max' => $template->getMaxValue()));
            }
        } else {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_productNumberOrTotal',
                    array('min' => $template->getMinValue(), 'max' => $template->getMaxValue(),
                          'minnr' => $template->getMinProducts(), 'maxnr' => $template->getMaxProducts()));
        }

        if ($template->getMinMinutesFromLastUpdate() >= 0 || $template->getMaxMinutesFromLastUpdate() >= 0) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_offset',
                array(
                    'min' => $template->getMinMinutesFromLastUpdate(),
                    'max' => $template->getMaxMinutesFromLastUpdate(),
                    'template_id' => $template->getTemplateId()
                ));
        }
        $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_products');

        $type = $template->getTemplateTypeModel();
        if ($type->getDndPeriod() > 0) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_wishlist_dnd',
                array('period'      => intval($type->getDndPeriod()),
                       'mail_number' => intval($type->getDndMailNumber())));
        }

        return $this;
    }

    /**
     * Get processor code.
     *
     * @return string
     */
    public function getProcessorCode() {
        return 'wishlist';
    }

    /**
     * Get price for.
     * @param Mage_Catalog_Model_Product $product
     * @param array $item
     * @return string
     */
    public function getPriceFor(Mage_Catalog_Model_Product $product, array $item) {
        /* @var $wishlistItem Mage_Wishlist_Model_Item */
        $wishlistItem = Mage::getModel('wishlist/item')->loadWithOptions($item[2]);
        try {
            return Mage::app()->getStore()
                    ->formatPrice($wishlistItem->getProduct()->getFinalPrice());
        } catch (Exception $e) {
            return "";
        }
    }
}
