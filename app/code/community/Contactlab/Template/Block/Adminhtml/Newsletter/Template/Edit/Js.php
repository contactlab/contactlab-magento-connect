<?php

/**
 * Contactlab template block adminhtml newsletter template edit js.
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Edit_Js extends Mage_Adminhtml_Block_Template {
    /**
     * Get template type options json.
     *
     * @return string
     */
    public function getTemplateTypeOptionsJson() {
        $rv = array();

        $types = Mage::getResourceModel('contactlab_template/type_collection');
        foreach ($types as $type) {
            $rv[$type->getEntityId()] = $this->getTemplateTypeOptionFor($type->getTemplateTypeCode(), $type->getIsCronEnabled());
        }
        return json_encode($rv);
    }

    /**
     * Get template type option for.
     *
     * @param unknown $code
     * @return void
     */
    public function getTemplateTypeOptionFor($code, $cron) {
        $rv = new stdClass();
        $rv->useCron = $cron ? true : false;
        $rv->alertCronChange = $this->__($cron
            ? "This template type would use Cron queue, would you like to activate it?"
            : "This template type wouldn't use Cron queue, would you like to deactivate it?");
        $rv->alertOptionChange = $this->__("Reload default values for customers' filter options?");
        if ($code === 'CART' || $code === 'WISHLIST') {
            $rv->visible = true;
            $rv->min_minutes_from_last_update = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/min_minutes_from_last_update');
            $rv->max_minutes_from_last_update = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/max_minutes_from_last_update');
            $rv->min_value = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/min_value');
            $rv->max_value = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/max_value');
            $rv->min_products = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/min_products');
            $rv->max_products = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/max_products');
            $rv->and_or = Mage::getStoreConfig('contactlab_template/'
                . strtolower($code) . '/and_or');
        } else {
            $rv->visible = false;
        }
        return $rv;
    }
}
