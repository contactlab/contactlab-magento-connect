<?php

class Contactlab_Commons_Model_System_Config_Source_Connection_Type {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 0,
                'label' => Mage::helper('contactlab_commons')->__('Remote server')),
            array('value' => 1,
                'label' => Mage::helper('contactlab_commons')->__('Local server')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return array(
            0 => Mage::helper('contactlab_commons')->__('Remote server'),
            1 => Mage::helper('contactlab_commons')->__('Local server'),
        );
    }

}
