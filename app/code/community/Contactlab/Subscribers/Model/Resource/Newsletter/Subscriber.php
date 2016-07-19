<?php

/**
 * Allow multi website newsletter subscriber.
 * Class Contactlab_Subscribers_Model_Resource_Newsletter_Subscriber
 */
class Contactlab_Subscribers_Model_Resource_Newsletter_Subscriber
    extends Mage_Newsletter_Model_Resource_Subscriber
{
    /**
     * Load subscriber from DB by email
     *
     * @param string $subscriberEmail
     * @return array
     */
	public function loadByEmail($subscriberEmail, $storeId=null)
    {   	  
        if (!$this->_isEnabledMultiWebsiteSubscriber()) {
            return parent::loadByEmail($subscriberEmail);
        }
        
        if(!$storeId)
        {
	        /** @var $customerSession Mage_Customer_Model_Session */
	        $customerSession = Mage::getSingleton('customer/session');
	        $ownerId = Mage::getModel('customer/customer')
	            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
	            ->loadByEmail($subscriberEmail)
	            ->getId();
	
	        $storeId = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId()
	            ? $customerSession->getCustomer()->getStoreId()
	            : Mage::app()->getStore()->getId();
        }
        
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable())
            ->where('subscriber_email=:subscriber_email')
            ->where('store_id=:store_id'); // Add store ID for newsletters

        $result = $this->getReadConnection()->fetchRow($select, array(
            'subscriber_email' => $subscriberEmail,
            'store_id' => $storeId
        ));        
        if (!$result) {
            return array();
        }
        
        return $result;
    }

    /**
     * Load subscriber by customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    public function loadByCustomer(Mage_Customer_Model_Customer $customer)
    {
        if (!$this->_isEnabledMultiWebsiteSubscriber()) {
            return parent::loadByCustomer($customer);
        }
        $select = $this->_read->select()
            ->from($this->getMainTable())
            ->where('customer_id=:customer_id')
            ->where('store_id=:store_id');

        $result = $this->_read->fetchRow($select, array(
            'customer_id' => $customer->getId(),
            'store_id' => $customer->getStoreId()
        ));

        if ($result) {
            return $result;
        }

        $select = $this->_read->select()
            ->from($this->getMainTable())
            ->where('subscriber_email=:subscriber_email')
            ->where('store_id=:store_id');

        $result = $this->_read->fetchRow($select, array(
            'subscriber_email' => $customer->getEmail(),
            'store_id' => $customer->getStoreId()
        ));

        if ($result) {
            return $result;
        }

        return array();
    }

    /**
     * Check configuration and Magento version.
     * @return bool
     */
    private function _isEnabledMultiWebsiteSubscriber()
    {
        return $this->_isEnabledConfiguration() && $this->_greaterThan14();
    }

    /**
     * Is rewrite enabled.
     * @return bool
     */
    private function _isEnabledConfiguration()
    {
        return Mage::getStoreConfigFlag('contactlab_subscribers/global/enable_multiwebsite_subscription');
    }

    /**
     * Is Mage > 1.4.
     * @return bool
     */
    private function _greaterThan14()
    {
        if ($this->_isEnterprise()) {
            return true;
        }
        $version = Mage::getVersionInfo();
        return $version['major'] >= '1' && $version['minor'] > '4';
    }

    /**
     * Is Enterprise edition.
     * @return bool
     */
    private function _isEnterprise()
    {
        return Mage::getEdition() == Mage::EDITION_ENTERPRISE;
    }
}