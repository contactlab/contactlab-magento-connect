<?php


/** Newsletter queue collection. */
class Contactlab_Template_Model_Resource_Newsletter_Queue_Collection extends Mage_Newsletter_Model_Resource_Queue_Collection {
    /**
     * Add filter by only ready fot sending item
     *
     * @return Mage_Newsletter_Model_Resource_Queue_Collection
     */
    public function addOnlyForSendingFilter()
    {
        parent::addOnlyForSendingFilter();
        $this->getSelect()->where('main_table.task_id is null');
        return $this;
    }
}
