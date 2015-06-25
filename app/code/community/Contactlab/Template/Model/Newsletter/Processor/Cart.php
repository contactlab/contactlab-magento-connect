<?php

/**
 * Abandoned cart processor.
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
class Contactlab_Template_Model_Newsletter_Processor_Cart extends Contactlab_Template_Model_Newsletter_Processor_Abstract {
    /**
     * Apply subscribers filter.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @return $this
     */
    public function applySubscribersFilter(Mage_Newsletter_Model_Template $template) {
        $this->applyFilter('contactlab_template/newsletter_processor_filter_onlyCustomers');
        $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_distinctByType');

        // @deprecated
        // $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_have');

        if ($template->getAndOr() == 'AND') {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_productNumber',
                    array('min' => $template->getMinProducts(),
                          'max' => $template->getMaxProducts()));
            $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_totalValue',
                    array('min' => $template->getMinValue(),
                          'max' => $template->getMaxValue()));
        } else {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_productNumberOrTotal',
                    array('min' => $template->getMinValue(),
                          'max' => $template->getMaxValue(),
                          'minnr' => $template->getMinProducts(),
                          'maxnr' => $template->getMaxProducts()));
        }

        if ($template->getMinMinutesFromLastUpdate() >= 0 ||
            $template->getMaxMinutesFromLastUpdate() >= 0) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_offset',
                array(
                    'min' => $template->getMinMinutesFromLastUpdate(),
                    'max' => $template->getMaxMinutesFromLastUpdate(),
                    'template_id' => $template->getTemplateId()
                ));
        }
        $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_products');

        $type = $template->getTemplateTypeModel();
        if ($type->getDndPeriod() > 0) {
            $this->applyFilter('contactlab_template/newsletter_processor_filter_cart_dnd',
                array(
                    'period'      => intval($type->getDndPeriod()),
                    'mail_number' => intval($type->getDndMailNumber())
                ));
        }

        return $this;
    }


    /**
     * Get processor code.
     *
     * @return string
     */
    public function getProcessorCode() {
        return 'cart';
    }

    /**
     * Get price for.
     * @param Mage_Catalog_Model_Product $product
     * @param array $item
     * @return string
     */
    public function getPriceFor(Mage_Catalog_Model_Product $product, array $item) {
        return Mage::app()->getStore()->formatPrice(floatval($item[3]));
    }
}
