<?php

/**
 * Source template type model (for options html elements)
 */
class Contactlab_Template_Model_System_Config_Source_Template_Type {
    /** Cached. */
    protected $_options;

    /**
     * To option array, value label pairs.
     *
     * @param unknown $isMultiselect = false
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('contactlab_template/type_collection')->loadData()->toOptionArray('entity_id');
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}
