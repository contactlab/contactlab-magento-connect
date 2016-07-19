<?php

/**
 * Observer for manage uk table.
 */
class Contactlab_Subscribers_Model_Observer_Uk extends Mage_Core_Model_Abstract {
    // Customer to delete
    private $toDeleteCustomers = array();
    private $toDeleteRecords = array();
    private $toUnsubscribeRecords = array();

    /** A subscriber has been saved. */
 	public function subscriberSaved($observer) {    	
        $subscriber = $observer->getEvent()->getDataObject();        
        $customerId = $subscriber->getCustomerId();
    	/* Added this control to prevent UK Integrity constraint violation */    
        if($subscriber->getSavedCustomerId())
        {
        	$customerId = $subscriber->getSavedCustomerId();
        }
               
        if ($customerId == 0) {
            $customerId = NULL;
        }
        Mage::helper("contactlab_subscribers/uk")
            ->update($customerId, $subscriber->getSubscriberId());
    }

    /** A customer has been saved. */
    public function customerSaved($observer) {
        $customer = $observer->getEvent()->getDataObject();
        Mage::helper("contactlab_subscribers/uk")->update(
            $customer->getEntityId(), NULL);
    }

    /**
     * After subscriber delete (add to queue).
     */
    public function afterSubscriberDelete($observer) {
        $source = $observer->getEvent()->getDataObject();
        $this->_doRecordDelete($source);
        $this->purge($observer);
    }

    /**
     * After customer delete (add to queue).
     */
    public function afterCustomerDelete($observer) {
        $source = $observer->getEvent()->getDataObject();
        $this->_doRecordDelete($source);
        $this->purge($observer);
        
        /* This will delete the customer fixing the MultiWebsite Subscriber */
        $customer = $observer->getEvent()->getCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')
        ->loadByEmail($customer->getEmail(), $customer->getStoreId());
        if($subscriber->getId()) {
        	$subscriber->delete();
        }
    }

    /**
     * Something has been deleted, purge.
     */
    public function purge($observer) {
        Mage::helper("contactlab_subscribers/uk")->purge();
    }

    /**
     * Before subscriber delete.
     */
    public function beforeSubscriberDelete($observer) {
        $subscriber = $observer->getEvent()->getDataObject();
        $hasCustomer = $subscriber->hasCustomerId() && $subscriber->getCustomerId() != 0;
        $wasCustomer = in_array($subscriber->getCustomerId(), $this->toDeleteCustomers);
        $uk = Mage::helper("contactlab_subscribers/uk")->searchBySubscriberId($subscriber->getSubscriberId());
        if ($uk) {
            if ($hasCustomer && !$wasCustomer) {
                $this->_queueUnsubscribeDelete($subscriber, $subscriber->getSubscriberEmail(), $subscriber->getStoreId(), $uk->getEntityId());
            } else {
                $this->_queueRecordDelete($subscriber, $subscriber->getSubscriberEmail(), $wasCustomer, $uk->getEntityId());
            }
        }
    }

    /**
     * Before customer delete.
     */
    public function beforeCustomerDelete($observer) {
        $customer = $observer->getEvent()->getDataObject();
        $subscriber = Mage::getModel("newsletter/subscriber")->loadByCustomer($customer);
        if ($subscriber->hasSubscriberId()) {
            $this->toDeleteCustomers[] = $customer->getEntityId();
        } else {
            /* @var $helper Contactlab_Subscribers_Helper_Uk */
            $helper = Mage::helper("contactlab_subscribers/uk");
            $uk = $helper->searchByCustomerId($customer->getEntityId());
            if ($uk !== false) {
                $this->_queueRecordDelete($customer, $customer->getEmail(), true, $uk->getEntityId());
            }
        }
    }

    /**
     * Queue unsubscription delete.
     */
    private function _queueUnsubscribeDelete($source, $email, $storeId, $uk) {
        $sourceId = $source->getResourceName() . "_" . $source->getId();
        $this->_log("_queueUnsubscribeDelete($sourceId)");
        $this->toUnsubscribeRecords[$sourceId] = array(
            'email' => $email,
            'store_id' => $storeId,
            'id' => $uk
        );
    }

    /**
     * Queue record delete.
     */
    private function _queueRecordDelete($source, $email, $isCustomer, $id) {
        $sourceId = $source->getResourceName() . "_" . $source->getId();
        $this->_log("_queueRecordDelete($sourceId)");
        $this->toDeleteRecords[$sourceId] = array(
            'email' => $email,
            'is_customer' => $isCustomer,
            'id' => $id
        );
    }

    /**
     * Do record delete.
     */
    private function _doRecordDelete($source) {
        $sourceId = $source->getResourceName() . "_" . $source->getId();
        if (array_key_exists($sourceId, $this->toDeleteRecords)) {
            $r = $this->toDeleteRecords[$sourceId];
            $this->_log("_doRecordDelete($sourceId)");
            Mage::getModel('contactlab_commons/deleted')
                    ->setModel($source->getResourceName())
                    ->setEmail($r['email'])
                    ->setEntityId($r['id'])
                    ->setIsCustomer($r['is_customer'])
                    ->save();
        }
        if (array_key_exists($sourceId, $this->toUnsubscribeRecords)) {
            $r = $this->toUnsubscribeRecords[$sourceId];
            $this->_syncUnsubscribe($r['email'], $r['id'], $r['store_id']);
        }
    }

    /** A newsletter_subscriber record has been deleted from backend! */
    private function _syncUnsubscribe($email, $id, $storeId) {
        $this->_log("_syncUnsubscribe($email, $id, $storeId)");
        Mage::helper("contactlab_subscribers")
            ->updateSubscriberStatus($email, $id, false, $storeId);
    }
    
    /** Log value. */
    private function _log($value) {
        Mage::helper("contactlab_commons")->logDebug($value);
    }
}
