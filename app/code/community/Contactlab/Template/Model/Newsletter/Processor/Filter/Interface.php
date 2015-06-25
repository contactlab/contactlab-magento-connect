<?php

/** Newsletter processor filter interface. */
interface Contactlab_Template_Model_Newsletter_Processor_Filter_Interface {

    /**
     * Apply filter.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return a collection
     */
    function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array());

    /**
     * Set store id.
     *
     * @param String $storeId
     */
    function setStoreId($storeId);

    /**
     * Get store id.
     *
     * @return string
     */
    function getStoreId();

    /**
     * Do run in test mode?
     *
     * @return boolean
     */
    function doRunInTestMode();
    
    /**
     * Send to all customers?
     * 
     * @return boolean
     */
    function doSendToAllCustomers();

    /**
     * Get filter name for debug.
     * @return string
     */
    function getName();
}
