<?php

/**
 * Template type model.
 *
 * @method String getTemplateTypeCode()
 */

/**
 * @method int getEntityId()
 * @method Contactlab_Template_Model_Type setEntityId($value)
 * @method string getName()
 * @method Contactlab_Template_Model_Type setName($value)
 * @method string getTemplateTypeCode()
 * @method Contactlab_Template_Model_Type setTemplateTypeCode($value)
 * @method int getIsSystem()
 * @method Contactlab_Template_Model_Type setIsSystem($value)
 * @method int getIsCronEnabled()
 * @method Contactlab_Template_Model_Type setIsCronEnabled($value)
 * @method int getDndPeriod()
 * @method Contactlab_Template_Model_Type setDndPeriod($value)
 * @method int getDndMailNumber()
 * @method Contactlab_Template_Model_Type setDndMailNumber($value)
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
