<?php

/**
 * Exporter data helper.
 */
class Contactlab_Subscribers_Helper_Data extends Mage_Core_Helper_Abstract {
    /** Skip subscribe customer function. */
    private $_skipSubscribeCustomer = false;

    /** Skip unsubscribe soap call. */
    private $_skipUnsubscribeSoapCall = false;

    private $_timezoneOffset = 0;

    /** Constructor. */
    public function __construct() {
        $this->_isDebug = Mage::helper('contactlab_commons')->isDebug();
        $this->_timezoneOffset = $this->_getTimezoneOffset();
    }

    private $_toUpdate = array();
    
    /** Check if module is enabled. */
    public function isEnabled(Contactlab_Commons_Model_Task $task) {
        return $task->getConfigFlag("contactlab_subscribers/global/enabled");
    }

    /** Check if module is enabled (Contactlab => Mage). */
    public function isEnabledContactlab2Magento(Contactlab_Commons_Model_Task $task) {
        return $task->getConfigFlag("contactlab_subscribers_2/setup/enabled");
    }

    /** Update customer stats. */
    public function updateCustomerStats($customerId) {
        if ($customerId) {
            $this->_toUpdate[] = $customerId;
        }
    }
    
    /** Really update stats. */
    public function doUpdateStats() {
        foreach ($this->_toUpdate as $id) {
            $customer = Mage::getModel('customer/customer')->load($id);
            if ($customer->getId()) {
                $this->doUpdateCustomerStats($customer);
            }
        }
    }
    
    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Core_Model_Abstract|null
     * @throws Exception
     */
    public function doUpdateCustomerStats(Mage_Customer_Model_Customer $customer) {
        $stats = Mage::getModel('contactlab_subscribers/stats')
                ->getCollection()
                ->addFieldToFilter("customer_id", $customer->getEntityId());
        $found = false;
        $rv = null;
        foreach ($stats as $stat) {
            $found = true;
            $rv = $stat->updateFromCustomer($customer)->save();
        }
        if (!$found) {
            $rv = Mage::getModel('contactlab_subscribers/stats')
                ->setCustomerId($customer->getEntityId())
                ->updateFromCustomer($customer)->save();
        }
        return $rv;
    }

    /**
     * Add clear statistics task to queue.
     * @return Contactlab_Commons_Model_Task
     */
    public function addClearStatsQueue() {
        return Mage::getModel("contactlab_commons/task")
                ->setTaskCode("ClearStatisticsTask")
                ->setModelName('contactlab_subscribers/task_clearStatsRunner')
                ->setDescription('Clear customer statistics')
                ->save();
    }

    /** Add calc statistcs task to queue. */
    public function addCalcStatsQueue() {
        return Mage::getModel("contactlab_commons/task")
                ->setTaskCode("CalcStatisticsTask")
                ->setModelName('contactlab_subscribers/task_calcStatsRunner')
                ->setDescription('Calc customer statistics')
                ->save();
    }

    /** Calculate batch all statistics. */
    public function calcStatistcs(Contactlab_Commons_Model_Task $task) {
        Mage::helper("contactlab_commons")->logInfo("Start calculating statistics");

        // First, remove all stats
        $this->addClearStatsQueue()->runTask();

        // Query all customers newsletter subscribers
        $customers = Mage::getModel("customer/customer")->getCollection()
                ->addAttributeToSelect('entity_id')->addAttributeToSelect('prefix')
                ->addAttributeToSelect('middlename')->addAttributeToSelect('suffix')
                ->addAttributeToSelect('lastname')->addAttributeToSelect('firstname');
        $customers->getSelect()->where("entity_id in (select customer_id from "
                . $customers->getResource()->getTable('sales/order')
                . ")");

        Mage::helper("contactlab_commons")->enableDbProfiler();
        $count = $customers->count();
        $task->setMaxValue($count);
        $index = 0;
        foreach ($customers as $customer) {
            $index++;
            if ($index % 500 == 0) {
                $logDescr = sprintf("[%5d / %-5d] Calculating statistcs for %s",
                    $index, $count, $customer->getName());
                if ($index % 500 == 0) {
                    // Add event every 500 rows
                    $task->addEvent($logDescr);
                    $task->setProgressValue($index);
                } else {
                    // Add a log every 500 rows
                    Mage::helper("contactlab_commons")->logInfo($logDescr);
                }
            }
            $this->doUpdateCustomerStats($customer);
        }
        $task->setProgressValue($count);
        Mage::helper("contactlab_commons")->logInfo("Statistics done");
        Mage::helper("contactlab_commons")->flushDbProfiler();
    }
    
    /** Unsubscribe email.
     * @param Contactlab_Commons_Model_Task $task
     * @param $email
     * @param $uk
     * @param $datetime
     * @param bool $logIt
     * @return bool
     */
    public function unsubscribe(Contactlab_Commons_Model_Task $task, $email, $uk, $datetime, $logIt = false) {
        if (!empty($uk)) {
            // $uk, int value of xml string content, not empty if it's a number > 0
            /** @var $ukModel Contactlab_Subscribers_Model_Uk */
            $ukModel = Mage::getModel('contactlab_subscribers/uk')->load($uk);
            if ($ukModel->hasEntityId() && $ukModel->hasSubscriberId()) {
                /** @var $model Contactlab_Subscribers_Model_Newsletter_Subscriber */
                $model = Mage::getModel("newsletter/subscriber")
                    ->load($ukModel->getSubscriberId());
            }
        }
        // 03/02/2015 - Disabled match by email address
        /*
        if (!isset($model)) {
            $task->addEvent("Could not find \"$uk\" unique id during unsubscription, trying with email \"$email\" address");
            $model = Mage::getModel("newsletter/subscriber")->loadByEmail($email);
        }
        */
        if (isset($model) && $model->hasSubscriberId()) {
            if (!$this->_checkCanUnsubscribe($task, $model, $datetime)) {
                return false;
            }
            $model->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
            $model->setLastSubscribedAt(NULL);
            $model->save();
            if ($logIt) {
                Mage::helper('contactlab_commons')->logNotice("$email has been unsubscribed");
            }
            return true;
        } else {
            $task->addEvent(sprintf("Could not find \"%s\" with uk %s during unsubscription", $email, $uk));
            return false;
        }
    }

    /**
     * If the subscriber has subscribed after ContactlLab subscription, wont do it!
     * @param Contactlab_Commons_Model_Task $task
     * @param Contactlab_Subscribers_Model_Newsletter_Subscriber $subscriber
     * @param $datetime
     * @return bool
     */
    private function _checkCanUnsubscribe(Contactlab_Commons_Model_Task $task,
            Contactlab_Subscribers_Model_Newsletter_Subscriber $subscriber,
            $datetime) {
        if (!$subscriber->isSubscribed()) {
            $task->addEvent(sprintf("\"%s\" is already unsubscribed", $subscriber->getEmail()));
            return false;
        }

        $lastSubscribedAt = Mage::getModel("core/date")->timestamp($subscriber->getLastSubscribedAt());
        $eventDatetime    = Mage::getModel("core/date")->timestamp($datetime) - $this->_timezoneOffset;

        $lastSubscribedAtStr = date("d/m/Y H:i:s", $lastSubscribedAt);
        $eventDatetimeStr    = date("d/m/Y H:i:s", $eventDatetime);


        if ($subscriber->hasLastSubscribedAt() && $lastSubscribedAt > $eventDatetime) {
            $task->addEvent(sprintf("\"%s\" has subscribed in website (%s) after ContactLab unsubscription (%s)!",
                    $subscriber->getEmail(),
                    $lastSubscribedAtStr,
                    $eventDatetimeStr));
            return false;
        } else if ($lastSubscribedAt <= $eventDatetime && $this->_isDebug) {
            $task->addEvent(sprintf("\"%s\" has subscribed in website (%s) before ContactLab unsubscription (%s)!",
                    $subscriber->getEmail(),
                    $lastSubscribedAtStr,
                    $eventDatetimeStr));
        }

        return true;
    }

    /** Timezone offset from CET to GMT. */
    private static function _getTimezoneOffset() {
        $dtz = new DateTimeZone("CET");
        return $dtz->getOffset(new DateTime("now", $dtz));
    }

    /** Update subscriber status via SOAP.
     * @param $email
     * @param $id
     * @param $isSubscribed
     * @param $storeId
     * @param bool $queue
     * @return $this
     */
    public function updateSubscriberStatus($email, $id, $isSubscribed, $storeId, $queue = true) {
        if ($this->_skipUnsubscribeSoapCall) {
            return $this;
        }
        if (!Mage::getStoreConfigFlag("contactlab_subscribers/global/soap_call_set_subscribed", $storeId)) {
            return $this;
        }
        
        try {
            Mage::helper("contactlab_commons")->logNotice("updateSubscriberStatus($email, $id, $isSubscribed, $storeId, $queue)");
            /** @var $call Contactlab_Subscribers_Model_Soap_SetSubscriptionStatus */
            $call = Mage::getModel('contactlab_subscribers/soap_setSubscriptionStatus');
            $call->setStoreId($storeId);
            $call->setEntityId($id);
            $call->setSubscriberEmail($email);
            $call->setSubscriberStatus($isSubscribed);
            $rv = $call->singleCall();
            return $rv;
        } catch (Contactlab_Subscribers_Model_Soap_SubscriberNotFoundException $e) {
            // Do nothing.
        } catch (Exception $e) {
            Mage::helper("contactlab_commons")->logCrit($e->getMessage());
            if ($queue) {
                $this->_doQueueUpdateSubscriberStatus($email, $id, $isSubscribed, $storeId);
            }
        }
        return $this;
    }

    /**
     * Queue update subscriber status.
     * @param $email String
     * @param $id int
     * @param $isSubscribed bool
     * @param $storeId int
     * @throws Exception
     */
    private function _doQueueUpdateSubscriberStatus($email, $id, $isSubscribed, $storeId) {
        $data = new stdClass();
        $data->email = $email;
        $data->entityId = $id;
        $data->storeId = $storeId;
        $data->isSubscribed = $isSubscribed;
        Mage::getModel("contactlab_commons/task")
                ->setTaskCode("UpdateSubscriberStatusRunner_$email")
                ->setStoreId($storeId)
                ->setModelName('contactlab_subscribers/task_updateSubscriberStatusRunner')
                ->setDescription("Update subscriber status runner of \"$email\" user")
                ->setTaskData(json_encode($data))
                ->save();
    }

    /**
     * Have to skip Subscribe Customer?
     */
    public function skipSubscribeCustomer() {
        return $this->_skipSubscribeCustomer;
    }

    /**
     * Unset skip Subscribe Customer?
     */
    public function unsetSkipSubscribeCustomer() {
        $this->_skipSubscribeCustomer = false;
    }

    /**
     * Set skip Subscribe Customer.
     */
    public function setSkipSubscribeCustomer() {
        $this->_skipSubscribeCustomer = true;
    }

    /**
     * Set skip unsubscribe soap call.
     */
    public function setSkipUnsubscribeSoapCall() {
        $this->_skipUnsubscribeSoapCall = true;
    }

    /**
     * Send notification email.
     * @param $recipient
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function sendSubscriberEditEmail($recipient)
    {
        if (!Mage::getStoreConfigFlag('contactlab_subscribers/modify_email/enable')) {
            return $this;
        }
        $translate = Mage::getSingleton('core/translate');

        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template');

        $templateId = Mage::getStoreConfig('contactlab_subscribers/modify_email/send_email_template',
            Mage::app()->getStore()->getId());
        $senderEmail = Mage::getStoreConfig('contactlab_subscribers/modify_email/email_sender');
        $senderName = Mage::getStoreConfig('contactlab_subscribers/modify_email/email_sender_name');

        /** @var $subscriber Contactlab_Subscribers_Model_Newsletter_Subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber')->load($recipient, 'subscriber_email');
        if (!$subscriber->hasSubscriberConfirmCode()) {
            $subscriber->setSubscriberConfirmCode($subscriber->randomSequence())->save();
        }
        $params = array('id' => $subscriber->getSubscriberId(), 'hash' => $subscriber->getSubscriberConfirmCode());

        //get the store associated with the subscriber and append it to final URL
        if ($subscriber->hasStoreId()) {
            $store = '?__store=' . Mage::getModel('core/store')->load($subscriber->getStoreId())->getCode();
        } else {
            $store = '';
        }

        $mailTemplate->sendTransactional(
            $templateId,
            array('name' => $senderName, 'email' => $senderEmail),
            trim($recipient),
            Mage::helper('contactlab_commons')->__('Modify your data'),
            array('url' => Mage::getUrl('contactlab_subscribers/edit', $params) . $store),
            Mage::app()->getStore()->getId()
        );
        $translate->setTranslateInline(true);

        return $this;
    }
}
