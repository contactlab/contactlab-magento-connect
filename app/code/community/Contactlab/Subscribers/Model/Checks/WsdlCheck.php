<?php

/**
 * Created by PhpStorm.
 * User: andreag
 * Date: 06/10/15
 * Time: 9.30
 */
class Contactlab_Subscribers_Model_Checks_WsdlCheck extends Contactlab_Subscribers_Model_Checks_AbstractCheck
{

    /**
     * Start check.
     * @return String
     */
    protected function doCheck()
    {
        return $this->_getCheckWsdl()
            ? $this->success(sprintf("WSDL test ok"))
            : $this->error(sprintf("WSDL test error"));
    }

    /**
     * Get code.
     * @return String
     */
    public function getCode()
    {
        return "wsdl";
    }

    /**
     * Get description.
     * @return String
     */
    public function getDescription()
    {
        return "WSDL test";
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 200;
    }

    /**
     * Check wsdl.
     */
    private function _getCheckWsdl()
    {
        $wsdl = Mage::getStoreConfig("contactlab_commons/soap/wsdl_url");
        $xml = @simplexml_load_file($wsdl);
        if (!$xml) {
            return false;
        }
        new SoapClient($wsdl, array('soap_version' => SOAP_1_2));
        return true;
    }

    /**
     * Should the check fail in test mode?
     * @return bool
     */
    public function shouldFailInTest() {
        return true;
    }

}