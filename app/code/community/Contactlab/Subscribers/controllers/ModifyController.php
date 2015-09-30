<?php

/**
 * Manages insertion of mail from user who wants to unsubscribe/change her data
 */
class Contactlab_Subscribers_ModifyController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     * takes as query parameter the mail of the subscriber
     */
    public function indexAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session = Mage::getSingleton('core/session');
            $email = (string) $this->getRequest()->getPost('email');
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }
                if (!$this->_isEmailPresent($email)) {
                    Mage::throwException($this->__('This email is not in our subscribers database'));
                }
                Mage::helper('contactlab_subscribers')->sendSubscriberEditEmail($email);
                Mage::getSingleton('core/session')->addSuccess($this->__('Follow the link sent to your email to modify your data'));
            } catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with your request: %s', $e->getMessage()));
            } catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with your request.'));
            }
        }
        $this->_redirectReferer();

    }

    public function personalAction()
    {
        $params = $this->getRequest()->getParams();
        Mage::dispatchEvent('contactlab_subscribers_subscriber_update', $params);
        $this->_redirectUrl(Mage::getBaseUrl());
    }

    /**
     * Is mail present
     * @param $email
     * @return bool
     */
    private function _isEmailPresent($email)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        if ($ownerId) {
            return true;
        }
        /** @var $subscriber Contactlab_Subscribers_Model_Newsletter_Subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber')->load($email, 'subscriber_email');
        return $subscriber->hasSubscriberId();
    }
}
