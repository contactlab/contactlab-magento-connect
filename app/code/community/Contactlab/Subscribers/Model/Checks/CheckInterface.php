<?php

interface Contactlab_Subscribers_Model_Checks_CheckInterface
{
    const SUCCESS = "Ok";
    const ERROR = "Error";

    /**
     * Do Check.
     * @return int
     */
    public function check();

    /**
     * Get name.
     * @return String
     */
    public function getName();

    /**
     * Get code.
     * @return String
     */
    public function getCode();

    /**
     * Get description.
     * @return String
     */
    public function getDescription();

    /**
     * Get Exit Code.
     * @return String
     */
    public function getExitCode();

    /**
     * Get position.
     * @return int
     */
    public function getPosition();

    /**
     * Get error array.
     * @return array
     */
    public function getErrors();

    /**
     * Get success array.
     * @return array
     */
    public function getSuccess();

    /**
     * Is an essential check
     * @return boolean
     */
    public function isEssential();

    /**
     * Should the check fail in test mode?
     * @return bool
     */
    public function shouldFailInTest();
}