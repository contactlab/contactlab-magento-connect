<?php

/**
 * Contactlab template model newsletter processor filter abstract.
 *
 * @method bool getSendToAllCustomers()
 * @method Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract setSendToAllCustomers($value)
 * @abstract
 */
abstract class Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract
        extends Varien_Object
        implements Contactlab_Template_Model_Newsletter_Processor_Filter_Interface {

    /**
     * Set Store Id.
     * @param String $value
     * @return mixed
     */
    public function setStoreId($storeId) {
        return parent::setStoreId($storeId);
    }

    /**
     * Get Store Id.
     * @return string
     */
    public function getStoreId() {
        return parent::getStoreId();
    }

    /**
     * Do run in test mode?
     *
     * @return false
     */
    public function doRunInTestMode() {
        return false;
    }

    /**
     * Get offset where.
     *
     * @param array $parameters
     * @param string $min
     * @param string $max
     * @return string
     */
    public function getOffsetWhere(array $parameters, $min = 'min', $max = 'max') {
        if (!$this->isEmtpy($parameters[$min]) && !$this->isEmtpy($parameters[$max])) {
            $condition = " between " . (60 * $parameters[$min]) . ' and ' . (60 * $parameters[$max]);
        } else if (!$this->isEmtpy($parameters[$max])) {
            $condition = " <= " . 60 * $parameters[$max];
        } else if (!$this->isEmtpy($parameters[$min])) {
            $condition = " >= " . 60 * $parameters[$min];
        } else {
            $condition = " < -1";
        }

        return $condition;
    }

    /**
     * Get offset where.
     *
     * @param array $parameters
     * @param string $min
     * @param string $max
     * @return string
     */
    public function getOffsetWhereQty(array $parameters, $min = 'min', $max = 'max') {
        if (!$this->isEmtpy($parameters[$min]) && !$this->isEmtpy($parameters[$max])) {
            $condition = " between " . ($parameters[$min]) . ' and ' . ($parameters[$max]);
        } else if (!$this->isEmtpy($parameters[$max])) {
            $condition = " <= " . $parameters[$max];
        } else if (!$this->isEmtpy($parameters[$min])) {
            $condition = " >= " . $parameters[$min];
        } else {
            $condition = " < -1";
        }

        return $condition;
    }

    /**
     * Emtpy value?
     *
     * @param string $value
     * @return boolean
     */
    public function isEmtpy($value) {
        return trim($value) == '';
    }

    /**
     * Send to all customers?
     * 
     * @return boolean
     */
    public function doSendToAllCustomers() {
        return $this->getSendToAllCustomers();
    }

    /**
     * Is this collection a customer collection?
     * @param Varien_Data_Collection_Db $collection
     * @return bool
     */
    public function isCustomerCollection(Varien_Data_Collection_Db $collection) {
        return $collection instanceof Mage_Customer_Model_Resource_Customer_Collection;
    }

    /**
     * Log collection sql for debug purpose.
     * @param Varien_Data_Collection_Db $collection
     */
    protected function logCollection(Varien_Data_Collection_Db $collection) {
        /* @var $h Contactlab_Commons_Helper_Data */
        $h = Mage::helper('contactlab_commons');
        if ($h->isDebug()) {
            $h->logDebug($collection->getSelect()->assemble());
        }
    }
}
