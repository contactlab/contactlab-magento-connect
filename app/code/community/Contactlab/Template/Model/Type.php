<?php

/**
 * Template type model.
 */
class Contactlab_Template_Model_Type extends Mage_Core_Model_Abstract {

    /** Construct. */
    public function _construct() {
        $this->_init("contactlab_template/type");
    }

    /**
     * Fixes toOption array bug (getData('id') is null).
     *
     * @param string $key
     * @param $index
     * @return string
     */
    public function getData($key='', $index=null) {
        if ($key == 'id') {
            return parent::getData($this->getIdFieldName(), $index);
        }
        return parent::getData($key, $index);
    }
}
