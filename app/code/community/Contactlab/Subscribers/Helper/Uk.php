<?php

/**
 * Uk manager.
 */
class Contactlab_Subscribers_Helper_Uk extends Mage_Core_Helper_Abstract {
    /**
     * 
     * @param numeric $customerId
     * @param numeric $subscriberId
     * @return void
     */
    public function update($customerId, $subscriberId) {
        if (is_null($customerId) && is_null($subscriberId)) {
            return;
        }
        if (is_null($subscriberId)) {
            $subscriberId = $this->_getSubscriberIdFromCustomerId($customerId);
        }
        if (is_null($subscriberId)) {
            // Not subscriber customer
            if (!$this->_exists('customer_id', $customerId)) {
                $this->_insertSubscriberIdCustomerId(NULL, $customerId);
            }
        } else if (is_null($customerId)) {
            // Subscriber not customer
            if (!$this->_exists('subscriber_id', $subscriberId)) {
                $this->_insertSubscriberIdCustomerId($subscriberId, NULL);
            }
        } else {
            // Subscriber customer
            if (!$this->_updateCustomerIdFromSubscriberId($subscriberId, $customerId)) {
                if (!$this->_updateSubscriberIdFromCustomerId($customerId, $subscriberId)) {
                    $this->_insertSubscriberIdCustomerId($subscriberId, $customerId);
                }
            }
        }
    }

    public function searchByCustomerId($id) {
        return $this->_search('customer_id', $id);
    }

    public function searchBySubscriberId($id) {
        return $this->_search('subscriber_id', $id);
    }

    /** Delete newsletter. */
    public function purge() {
        Mage::getModel("contactlab_subscribers/uk")->purge();
    }

    private final function _search($column, $id) {
        $model = Mage::getModel("contactlab_subscribers/uk")->load($id, $column);
        return $model->hasEntityId() ? $model : FALSE;
    }

    private final function _exists($column, $id) {
        return $this->_search($column, $id) ? TRUE : FALSE;
    }

    private final function _insertSubscriberIdCustomerId($subscriberId, $customerId) {
        $model = Mage::getModel("contactlab_subscribers/uk");
        return $model->setCustomerId($customerId)->setSubscriberId($subscriberId)->save();
    }

    private final function _updateSubscriberIdFromCustomerId($customerId, $subscriberId) {
        $model = Mage::getModel("contactlab_subscribers/uk")->load($customerId, 'customer_id');
        if (!$model->hasEntityId()) {
            return false;
        }
        return $model->setSubscriberId($subscriberId)->save();
    }

    private final function _updateCustomerIdFromSubscriberId($subscriberId, $customerId) {
        $model = Mage::getModel("contactlab_subscribers/uk")->load($subscriberId, 'subscriber_id');
        if (!$model->hasEntityId()) {
            return false;
        }
        if ($model->getCustomerId() == $customerId) {
            return $model;
        }
        $rv = $model->setCustomerId($customerId)->save();
        Mage::dispatchEvent('contactlab_subscribers_uk_subscriber_promoted_to_customer', array(
            'data_object' => $model));
        return $rv;
    }


    private function _getSubscriberIdFromCustomerId($customerId) {
        $customer = Mage::getModel("customer/customer")->load($customerId);
        $subscriber = Mage::getModel("newsletter/subscriber")->loadByCustomer($customer);
        return $subscriber->hasSubscriberId() ? $subscriber->getSubscriberId() : NULL;
    }

    /**
     * Update all.
     * @param boolean $doIt
     */
    public function updateAll($doIt, Contactlab_Commons_Model_Task $task = null) {
        /* @var $model Contactlab_Subscribers_Model_Uk */
        $model = Mage::getModel("contactlab_subscribers/uk");
        if (!is_null($task)) {
            $model->setTask($task);
        }
        $model->update($doIt);
        return !$model->getHasNotices();
    }

    /** Add update uk task. */
    public function addUpdateUkTask() {
        return Mage::getModel("contactlab_commons/task")
                ->setTaskCode("UpdateUkTask")
                ->setModelName('contactlab_subscribers/task_updateUkRunner')
                ->setDescription('Update uk table')
                ->save();
    }

    /**
     * Truncate table
     */
    public function truncate(Contactlab_Commons_Model_Task $task = null) {
        /* @var $model Contactlab_Subscribers_Model_Uk */
        $model = Mage::getModel("contactlab_subscribers/uk");
        if (!is_null($task)) {
            $model->setTask($task);
        }
        $model->truncate();
    }

    /** Add truncate uk task. */
    public function addTruncateUkTask() {
        return Mage::getModel("contactlab_commons/task")
                ->setTaskCode("TruncateUkTask")
                ->setModelName('contactlab_subscribers/task_truncateUkRunner')
                ->setDescription('Truncate uk table')
                ->save();
    }

}
