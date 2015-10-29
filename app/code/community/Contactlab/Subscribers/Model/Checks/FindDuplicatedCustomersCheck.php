<?php

/**
 * Class FindDuplicatedCustomersCheck
 */
class Contactlab_Subscribers_Model_Checks_FindDuplicatedCustomersCheck extends Contactlab_Subscribers_Model_Checks_AbstractCheck
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
            return $this->error(sprintf("Duplicated customers in newsletter subscribers: %d", $count));
        } else {
            return $this->success("No duplicated customers in newsletter subscribers");
        }
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "duplicated-customers-count";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Find duplicated customers check";
    }

    /**
     * Get customer count.
     * @return int
     */
    private function getCount()
    {
        $sql = sprintf("select count(1) as c from (select customer_id, count(1) from %s where customer_id != 0 group by customer_id having count(1) > 1) t;", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 130;
    }

    /**
     * Is essential check.
     * @return bool
     */
    public function isEssential()
    {
        return true;
    }
}