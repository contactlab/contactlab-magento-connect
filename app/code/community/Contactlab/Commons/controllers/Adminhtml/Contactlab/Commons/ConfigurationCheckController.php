<?php

/**
 * Test controller.
 */
class Contactlab_Commons_Adminhtml_Contactlab_Commons_ConfigurationCheckController extends Mage_Adminhtml_Controller_Action {

    /**
     * Index of release notes.
     */
    public function indexAction() {
        $this->_title($this->__('Configuration Check'));
        $this->loadLayout()->_setActiveMenu('newsletter/contactlab');
        return $this->renderLayout();
    }

    /**
     * Is this controller allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('newsletter/contactlab/configuration_check');
    }
    
    public function testemailAction(){      	
    	$toEmail = $this->getRequest()->getParam('mail');    	
    	if($toEmail)
    	{    	
    		$toName = $toEmail;
	    	$fromEmail = Mage::getStoreConfig('trans_email/ident_general/email');
	    	$fromName = Mage::getStoreConfig('trans_email/ident_general/name');
	    	
	    	$body = Mage::helper('contactlab_commons')->__('This is a Contactlab Test Email!');
	    	$subject = Mage::helper('contactlab_commons')->__('Contactlab Test Email');

	    	$mail = Mage::getModel('contactlab_transactional/zend_mail', 'utf-8');
	    	$mail->setBodyText($body);
	    	$mail->setFrom($fromEmail, $fromName);
	    	$mail->addTo($toEmail, $toName);
	    	$mail->setSubject($subject);
	    	try {
	    		$mail->send();	    		
	    		Mage::getSingleton('core/session')->addSuccess(Mage::helper('contactlab_commons')->__('Test email sent seccesfully.'));
	    	}
	    	catch(Exception $ex) {	    		
	    		Mage::getSingleton('core/session')->addError(Mage::helper('contactlab_commons')->__('Unable to send test email.'));
	    	}
	    
    	}
    	$this->_redirect('*/*/index');
    }
}
