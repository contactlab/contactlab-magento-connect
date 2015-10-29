<?php

/**
 * Log model.
 */

/**
 * @method int getLogId()
 * @method Contactlab_Commons_Model_Log setLogId(int $value)
 * @method string getCreatedAt()
 * @method Contactlab_Commons_Model_Log setCreatedAt(string $value)
 * @method int getLogLevel()
 * @method Contactlab_Commons_Model_Log setLogLevel(int $value)
 * @method string getDescription()
 * @method Contactlab_Commons_Model_Log setDescription(string $value)
 */
class Contactlab_Commons_Model_Log extends Mage_Core_Model_Abstract {

    /**
     * Construct.
     */
    public function _construct() {
        $this->_init("contactlab_commons/log");
    }

}
