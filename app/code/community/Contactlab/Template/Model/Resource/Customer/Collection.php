<?php


/**
 * Contactlab template model resource newsletter subscriber collection.
 */
class Contactlab_Template_Model_Resource_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection {
    /**
     * Get the real size.
     * @return int
     */
    public function getRealSize() {
        $sql = $this->getSelectCountSql();
        return $this->getConnection()->fetchOne($sql, $this->_bindParams);
    }
}
