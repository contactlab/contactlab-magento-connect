<?php

/**
 * Manages insertion of mail from user who wants to unsubscribe/change her data
 */
class Contactlab_Subscribers_EditController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     * takes as query parameter the mail of the subscriber
     */
    public function indexAction()
    {
        $params = $this->getRequest()->getParams();
        //check params
        if (!array_key_exists('hash', $params)
            || !array_key_exists('id', $params)) {
            Mage::throwException($this->__('Not a valid URL'));
        }

        // Load the subscriber and the additional fields entity

        $subs = Mage::getModel('newsletter/subscriber')->load($params['id']);
        if (!$subs->hasData())
            Mage::throwException($this->__('Subscriber not present'));

        /** @noinspection PhpUndefinedMethodInspection */
        if (!$subs->hasSubscriberConfirmCode())
            Mage::throwException($this->__('Subscriber has no confirm code'));

        if ($subs->getSubscriberConfirmCode() != $params['hash'])
            Mage::throwException($this->__('Confirm code does not match subscriber'));

        /** @var $fields Contactlab_Subscribers_Model_Fields */
        /** @noinspection PhpUndefinedMethodInspection */
        $fields = Mage::getModel('contactlab_subscribers/fields')->load($subs->getSubscriberId(), 'subscriber_id');

        if (!$fields->hasData())
            Mage::throwException($this->__('No additional fields for this subscriber'));
        //end of checks
        //register our fields entity, it will be used to fill input fields
        //by the Modify block
        Mage::register('contactlab/fields', $fields);

        $this->loadLayout();
        $this->renderLayout();
    }
}
