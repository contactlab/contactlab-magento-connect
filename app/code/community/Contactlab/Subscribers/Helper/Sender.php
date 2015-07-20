<?php

/**
 * Helper to send emails
 */
class Contactlab_Subscribers_Helper_Sender extends Mage_Core_Helper_Abstract {

    /**
     * Send notification email.
     */
    public function sendEmail($recipient,$emType) {
        if (/*!Mage::getStoreConfigFlag('contactlab_commons/modify_email/enable')*/false) {
            return;
        }
        $translate = Mage::getSingleton('core/translate');

        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template');

        $templateId = Mage::getStoreConfig('contactlab_commons/modify_email/send_email_template',
        	Mage::app()->getStore()->getId());
        $senderEmail = Mage::getStoreConfig('contactlab_commons/modify_email/email_sender');
        $senderName = Mage::getStoreConfig('contactlab_commons/modify_email/email_sender_name');
        
        $subsmod=Mage::getModel('newsletter/subscriber')->load($recipient,'subscriber_email');
        if(!$subsmod->hasSubscriberConfirmCode()){
            $subsmod->setSubscriberConfirmCode($subsmod->randomSequence())->save();
        }
        //Mage::helper('contactlab_commons')->logInfo(print_r($templateId,true));
        $params = array('id'=>$subsmod->getSubscriberId(), 'hash' => $subsmod->getSubscriberConfirmCode());
        
        //get the store associated with the subscriber and append it to final URL
        if($subsmod->hasStoreId())
            $store = '?__store=' . Mage::getModel('core/store')->load($subsmod->getStoreId())->getCode();
        else 
            $store = '';
        
        $mailTemplate->sendTransactional(
                $templateId,
                array('name' => $senderName,'email' => $senderEmail),
                trim($recipient),
                Mage::helper('contactlab_commons')->__('Modify your data'),
                array('url' => Mage::getUrl('contactlab/modify/showform',$params) . $store),
                Mage::app()->getStore()->getId()
        );
        $translate->setTranslateInline(true);

        return $this;
    }

}
