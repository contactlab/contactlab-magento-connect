<?php

/**
 * Uk manager helper.
 */
class Contactlab_Subscribers_Helper_Uk extends Mage_Core_Helper_Abstract
{
    /**
     * Update keys.
     * @param int $customerId
     * @param int $subscriberId
     * @return void
     */
    public function update($customerId, $subscriberId) {
        try {
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
        } catch (Exception $e) {
            $this->_reportException($e);
        }
    }

    /**
     * Search by customer id.
     * @param int $id
     * @return bool|Contactlab_Subscribers_Model_Uk
     */
    public function searchByCustomerId($id) {
        return $this->_search('customer_id', $id);
    }

    /**
     * Search by subscriber id.
     * @param int $id
     * @return bool|Contactlab_Subscribers_Model_Uk
     */
    public function searchBySubscriberId($id) {
        return $this->_search('subscriber_id', $id);
    }

    /** Delete newsletter. */
    public function purge() {
        Mage::getModel("contactlab_subscribers/uk")->purge();
    }

    /**
     * Search record.
     * @param string $column
     * @param int $id
     * @return bool|Contactlab_Subscribers_Model_Uk
     */
    private final function _search($column, $id) {
        $model = Mage::getModel("contactlab_subscribers/uk")->load($id, $column);
        return $model->hasEntityId() ? $model : false;
    }

    /**
     * Does the record exist?
     * @param string $column
     * @param int $id
     * @return bool
     */
    private final function _exists($column, $id) {
        return $this->_search($column, $id) ? true : false;
    }

    /**
     * Insert subscriber id and customer id.
     * @param int $subscriberId
     * @param int $customerId
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     */
    private final function _insertSubscriberIdCustomerId($subscriberId, $customerId) {
        /** @var $model Contactlab_Subscribers_Model_Uk */
        $model = Mage::getModel("contactlab_subscribers/uk");
        return $model->setCustomerId($customerId)->setSubscriberId($subscriberId)->save();
    }

    /**
     * Update subscriber id from customer id.
     * @param int $customerId
     * @param int $subscriberId
     * @return bool|Mage_Core_Model_Abstract
     * @throws Exception
     */
    private final function _updateSubscriberIdFromCustomerId($customerId, $subscriberId) {
        /** @var $model Contactlab_Subscribers_Model_Uk */
        $model = Mage::getModel("contactlab_subscribers/uk")->load($customerId, 'customer_id');
        if (!$model->hasEntityId()) {
            return false;
        }
        return $model->setSubscriberId($subscriberId)->save();
    }

    /**
     * Update customer id from subscriber id.
     * @param int $subscriberId
     * @param int $customerId
     * @return bool|Contactlab_Subscribers_Model_Uk|Mage_Core_Model_Abstract
     * @throws Exception
     */
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

    /**
     * Get subscriber id from customer id.
     * @param int $customerId
     * @return int|null
     */
    private function _getSubscriberIdFromCustomerId($customerId) {
        $customer = Mage::getModel("customer/customer")->load($customerId);
        /* @var $subscriber Contactlab_Subscribers_Model_Newsletter_Subscriber */
        $subscriber = Mage::getModel("newsletter/subscriber")->loadByCustomer($customer);
        return $subscriber->hasSubscriberId() ? $subscriber->getSubscriberId() : NULL;
    }

    /**
     * Update all.
     * @param boolean $doIt
     * @param Contactlab_Commons_Model_Task $task
     * @return bool
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

    /**
     * Add update uk task.
     */
    public function addUpdateUkTask() {
        return Mage::getModel("contactlab_commons/task")
            ->setTaskCode("UpdateUkTask")
            ->setModelName('contactlab_subscribers/task_updateUkRunner')
            ->setDescription('Update uk table')
            ->save();
    }

    /**
     * Truncate table
     * @param Contactlab_Commons_Model_Task $task
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

    /**
     * Report exception.
     * @param Exception $exception
     */
    private function _reportException(Exception $exception) {
        Mage::logException($exception);
        $v = $exception->getMessage() . " - " . $exception->getTraceAsString();
        Mage::helper('contactlab_commons')->logCrit($v);
        Mage::helper('contactlab_commons')->addCriticalMessage($v);
    }
}
