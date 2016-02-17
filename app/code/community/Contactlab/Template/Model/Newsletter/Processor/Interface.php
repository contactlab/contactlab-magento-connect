<?php

/** Processor interface. */
interface Contactlab_Template_Model_Newsletter_Processor_Interface {

    /**
     * Load subscribers interface function.
     *
     * @param Contactlab_Template_Model_Newsletter_Template $template
     * @param boolean $onlyCustomers
     * @return $this
     */
    function loadSubscribers(Contactlab_Template_Model_Newsletter_Template $template, $onlyCustomers);

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
     * Is enabled.
     *
     * @return bool
     */
    function isEnabled();
}
