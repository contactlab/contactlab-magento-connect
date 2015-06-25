<?php


/**
 * Contactlab template model resource newsletter subscriber collection.
 */
class Contactlab_Template_Model_Resource_Newsletter_Subscriber_Collection extends Mage_Newsletter_Model_Resource_Subscriber_Collection {
    /**
     * Set loading mode subscribers by queue
     *
     * @param Mage_Newsletter_Model_Queue $queue
     * @return Mage_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function useQueue(Mage_Newsletter_Model_Queue $queue) {
        $this->getSelect()
            ->join(array('link'=>$this->_queueLinkTable), "link.subscriber_id = main_table.subscriber_id", array('product_ids'))
            ->where("link.queue_id = ? ", $queue->getId());
        $this->_queueJoinedFlag = true;
        return $this;
    }

    /**
     * Get the real size.
     * @return int
     */
    public function getRealSize() {
        $sql = $this->getSelectCountSql();
        return $this->getConnection()->fetchOne($sql, $this->_bindParams);
    }
}
