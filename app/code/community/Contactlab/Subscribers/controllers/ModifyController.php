<?php

/**
 * Manages insertion of mail from user who wants to unsubscribe/change her data
 */
class Contactlab_Subscribers_ModifyController extends  Mage_Core_Controller_Front_Action {
    private function _isEmailPresent($email){
        $ownerId = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email)
                ->getId();
        if($ownerId) return true;
        $subsid = Mage::getModel('newsletter/subscriber')->load($email,'subscriber_email');
        return $subsid->hasSubscriberId();
    }
    /**
     * index action 
     * takes as query parameter the mail of the subscriber
     */
    public function indexAction() {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
            try{
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }
                if(!$this->_isEmailPresent($email)){
                    Mage::throwException($this->__('This email is not in our subscribers database'));
                }
                Mage::helper('contactlab_subscribers/sender')->sendEmail($email);
                Mage::getSingleton('core/session')->addSuccess($this->__('Follow the link sent to your email to modify your data'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with your request: %s', $e->getMessage()));
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with your request.'));
            }
        }
        $this->_redirectReferer();
        
    }
    
    public function showformAction() {
        $params = $this->getRequest()->getParams();
        //check params
        if(     !array_key_exists('hash', $params)
            ||  !array_key_exists('id', $params))
                Mage::throwException($this->__('Not a valid URL'));
        
        //Load the subscriber and the additional fields entity
       
        $subs = Mage::getModel('newsletter/subscriber')->load($params['id']);
        if(!$subs->hasData())
            Mage::throwException($this->__('Subscriber not present'));
        
        if(!$subs->hasSubscriberConfirmCode())
            Mage::throwException($this->__('Subscriber has no confirm code'));

        if($subs->getSubscriberConfirmCode() != $params['hash'])
            Mage::throwException($this->__('Confirm code does not match subscriber'));
        
        $fields = Mage::getModel('contactlab_subscribers/fields')->load($subs->getSubscriberId(), 'subscriber_id');
        
        if(!$fields->hasData())
            Mage::throwException($this->__('No additional fields for this subscriber'));
        //end of checks
        //register our fields entity, it will be used to fill input fields
        //by the Modify block
        Mage::register('contactlab/fields',$fields);

        $this->loadLayout();
        $this->renderLayout();
        
    }
    
    public function personalAction() {
        $params = $this->getRequest()->getParams();
        Mage::dispatchEvent('contactlab_subscribers_subscriber_update',$params);
        $this->_redirectUrl(Mage::getBaseUrl());
    }
}
