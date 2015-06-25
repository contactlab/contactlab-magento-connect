<?php


/** Newsletter templates resource. */
class Contactlab_Template_Model_Resource_Newsletter_Template extends Mage_Newsletter_Model_Resource_Template {

    /**
     * Prepare value for save. Empty string problem fix.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function _prepareTableValueForSave($value, $type) {
        $type = strtolower($type);
        if (($type == 'int' || $type == 'decimal' || $type == 'numeric' || $type == 'float') && $value == '') {
            return null;
        }
        return parent::_prepareTableValueForSave($value, $type);
    }
}
