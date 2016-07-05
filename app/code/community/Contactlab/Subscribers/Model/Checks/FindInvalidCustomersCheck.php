<?php

/**
 * Class FindInvalidCustomersCheck
 */
class Contactlab_Subscribers_Model_Checks_FindInvalidCustomersCheck
    extends Contactlab_Subscribers_Model_Checks_AbstractCheck
{
    private $count;

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->getCount();
        $this->count = $count;
        if ($count > 0) {
            return $this->error(sprintf("Non-valid customers in newsletter subscribers: %d", $count));
        } else {
            return $this->success("No non-valid customers in newsletter subscribers");
        }
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "invalid-customer-count";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Find non-valid customers check";
    }

    /**
     * Need Database?
     * @return bool
     */
    public function needDatabase()
    {
        return true;
    }

    /**
     * Get customer count.
     * @return int
     */
    private function getCount()
    {
        $sql = sprintf("select count(1) as c from %s where customer_id != 0 and customer_id not in (select entity_id from %s);",
            $this->_getTableName('newsletter_subscriber'),
            $this->_getTableName('customer_entity'));
        return $this->_getSqlResult($sql);
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 140;
    }

    /**
     * Get log data to send.
     * @return int
     */
    public function getLogData()
    {
        return $this->count;
    }

    /**
     * Do send log data.
     * @return bool
     */
    public function doSendLogData()
    {
        return true;
    }

    /**
     * Is essential check.
     * @return bool
     */
    public function isEssential()
    {
        return false;
    }
}