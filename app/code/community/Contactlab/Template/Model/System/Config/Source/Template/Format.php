<?php

/** Format type for template (html, text plain or both) */
class Contactlab_Template_Model_System_Config_Source_Template_Format {

    /**
     * Options getter.
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'H',
                'label' => Mage::helper('contactlab_template')->__('HTML only')),
            array('value' => 'T',
                'label' => Mage::helper('contactlab_template')->__('Text/Plain only')),
            array('value' => 'B',
                'label' => Mage::helper('contactlab_template')->__('Both HTML and Text/Plain')),
        );
    }

    /**
     * Options getter.
     * @return array
     */
    public function toArray() {
        $rv = array();
        foreach ($this->toOptionArray() as $item) {
            $rv[$item['value']] = $item['label'];
        }
        return $rv;
    }
}
