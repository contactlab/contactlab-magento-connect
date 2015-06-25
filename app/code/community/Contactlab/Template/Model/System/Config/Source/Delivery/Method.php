<?php

/**  Delivery method (auto or manual) */
class Contactlab_Template_Model_System_Config_Source_Delivery_Method {

    /**
     * Options getter.
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'auto',
                'label' => Mage::helper('contactlab_template')->__('Automatic')),
            array('value' => 'manual',
                'label' => Mage::helper('contactlab_template')->__('Manual')),
        );
    }
}
