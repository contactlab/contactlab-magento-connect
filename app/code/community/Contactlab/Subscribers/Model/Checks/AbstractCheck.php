<?php

abstract class Contactlab_Subscribers_Model_Checks_AbstractCheck
    extends Mage_Core_Model_Abstract
    implements Contactlab_Subscribers_Model_Checks_CheckInterface
{
    private $_success = array();
    private $_error = array();

    /**
     * Start check.
     * @return String
     */
    public function check() {
        $exitCode = $this->doCheck();
        $this->setExitCode($exitCode);
        return $exitCode;
    }

    /**
     * Get Name.
     * @return string
     */
    function getName()
    {
        return get_class($this);
    }

    /**
     * Start check.
     * @return String
     */
    protected abstract function doCheck();

    /**
     * @return mixed
     */
    public function getExitCode()
    {
        return parent::getData('exit_code');
    }

    /**
     * @param mixed $exitCode
     * @return Varien_Object
     */
    public function setExitCode($exitCode)
    {
        return parent::setData('exit_code', $exitCode);
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function success($value)
    {
        $this->_addSuccess($value);
        return self::SUCCESS;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function error($value)
    {
        Mage::logException(new Zend_Exception($value));
        $this->_addError($value);
        return self::ERROR;
    }

    /**
     * Table name with prefix.
     * @param $string
     * @return string
     */
    protected function _getTableName($string)
    {
        return Mage::getModel('core/resource')->getTableName($string);
    }

    /**
     * Get sql result.
     * @param $sql
     * @return int
     * @throws Zend_Exception
     */
    protected function _getSqlResult($sql)
    {
        /** @var $conn Varien_Db_Adapter_Pdo_Mysql */
        $conn = Mage::getModel('core/resource')->getConnection('read');
        /** @var $stmt PDOStatement */
        $stmt = $conn->prepare($sql);
        /** @noinspection PhpUndefinedVariableInspection */
        $stmt->bindColumn('c', $count);
        $stmt->execute();
        $stmt->fetch();
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Add success row.
     * @param String $value
     */
    private function _addSuccess($value)
    {
        $this->_success[] = $value;
    }

    /**
     * Add error row.
     * @param String $value
     */
    private function _addError($value)
    {
        $this->_error[] = $value;
    }

    /**
     * Get error messages.
     * @return array
     */
    public function getErrors()
    {
        return $this->_error;
    }

    /**
     * Get success messages.
     * @return array
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * Is essential check.
     * @return bool
     */
    public function isEssential()
    {
        return false;
    }

    /**
     * Should the check fail in test mode?
     * @return bool
     */
    public function shouldFailInTest() {
        return false;
    }

}