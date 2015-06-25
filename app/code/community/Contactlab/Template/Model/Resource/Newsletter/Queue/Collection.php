<?php


/** Newsletter queue collection. */
class Contactlab_Template_Model_Resource_Newsletter_Queue_Collection extends Mage_Newsletter_Model_Resource_Queue_Collection {
    private $_disableIdCheck = false;

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
    /**
     * Adding item to item array
     *
     * @param   Varien_Object $item
     * @return  Varien_Data_Collection
     */
    public function addItem(Varien_Object $item) {
        if ($this->_disableIdCheck) {
            return $this->_addItem($item);
        } else {
            return parent::addItem($item);
        }
    }

    /**
     * Disable id check.
     */
    public function disableIdCheck() {
        $this->_disableIdCheck = true;
    }

    /**
     * Enable id check.
     */
    public function enableIdCheck() {
        $this->_disableIdCheck = false;
    }
}
