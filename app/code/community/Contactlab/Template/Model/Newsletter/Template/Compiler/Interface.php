<?php

/**
 * Contactlab template model newsletter template compiler interface.
 */
interface Contactlab_Template_Model_Newsletter_Template_Compiler_Interface {
    /**
     * Compile.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @param Mage_Core_Model_Abstract $customer
     * @return string
     */
    function compile(Mage_Newsletter_Model_Template $template, Mage_Core_Model_Abstract $customer);
}
