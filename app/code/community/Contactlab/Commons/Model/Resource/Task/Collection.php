<?php

/**
 * Task colleciton.
 */
class Contactlab_Commons_Model_Resource_Task_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /** Construct. */
    public function _construct() {
        $this->_init("contactlab_commons/task");
    }

    /** Load tasks ready to be consumed. */
    public function loadReadyToConsumeTasks() {
        $rv = $this->addFieldToFilter("status", array(
            "in" => array(
                Contactlab_Commons_Model_Task::STATUS_NEW,
                Contactlab_Commons_Model_Task::STATUS_WAITING_FOR_RETRY
            ))
        );
		$date = Mage::getModel('core/date');
        $rv->getSelect()->where("(planned_at is null or planned_at <= ?)", $date->gmtDate());
        return $rv->load();
    }

    /** Old tasks. */
    public function loadOldTasks() {
        $rv = $this->addFieldToFilter("status", array(
            "in" => array(
                Contactlab_Commons_Model_Task::STATUS_CLOSED,
                Contactlab_Commons_Model_Task::STATUS_CANCELLED
            ))
        );
        $days = Mage::getStoreConfig("contactlab_commons/global/days_of_old_tasks");
		$date = Mage::getModel('core/date');
        if (is_numeric($days)) {
            $rv->getSelect()->where("created_at <= adddate(date(?), interval -" . $days . "  day)", $date->gmtDate());
        } else {
            $rv->getSelect()->where("created_at <= date(?)", $date->gmtDate());
        }
        // Send event for include other tasks
        Mage::dispatchEvent('contactlab_commons_task_collection_load_old_tasks', array(
            'collection' => $rv));
        return $rv->load();
    }

    /** Old tasks. */
    public function loadOldFailedTasks() {
        $rv = $this->addFieldToFilter("status", array(
            "in" => array(
                Contactlab_Commons_Model_Task::STATUS_FAILED
            ))
        );
        $days = Mage::getStoreConfig("contactlab_commons/global/days_of_old_tasks");
		$date = Mage::getModel('core/date');
        if (is_numeric($days)) {
        	$days = $days * 2;
            $rv->getSelect()->where("created_at <= adddate(date(?), interval -" . $days . "  day)", $date->gmtDate());
        } else {
            $rv->getSelect()->where("created_at <= date(?)", $date->gmtDate());
        }
        return $rv->load();
    }

    /** Old tasks. */
    public function loadVisibleTasks() {
        return $this->addFieldToFilter("status", array(
            "nin" => array(
                Contactlab_Commons_Model_Task::STATUS_HIDDEN
            ))
        );
    }
}
