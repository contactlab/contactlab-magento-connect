<?php

class Contactlab_Subscribers_Model_Checks_ContactlabAuthApiKeyCheck
    extends Contactlab_Subscribers_Model_Checks_AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $key = Mage::getStoreConfig('contactlab_template/queue/auth_api_key');
        if (empty($key)) {
            return $this->error("No auth_api_key specified");
        } else {
            return $this->success("auth_api_key specified");
        }
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "auth-api-key";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check auth_api_key";
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
     * Is essential check.
     * @return bool
     */
    public function isEssential()
    {
        return true;
    }


    /**
     * Should the check fail in test mode?
     * @return bool
     */
    public function shouldFailInTest()
    {
        return true;
    }
}