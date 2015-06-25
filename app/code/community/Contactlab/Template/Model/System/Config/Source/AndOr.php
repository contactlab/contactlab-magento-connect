<?php


/** And/Or source model for system config. */
class Contactlab_Template_Model_System_Config_Source_AndOr {

    /**
     * Options getter.
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'AND',
                'label' => Mage::helper('contactlab_template')->__('And')),
            array('value' => 'OR',
                'label' => Mage::helper('contactlab_template')->__('Or')),
        );
    }
}
