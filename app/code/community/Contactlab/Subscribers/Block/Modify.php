<?php


/**
 * Newsletter modify block
 *
 * @category   Contactlab
 * @package    Contactlab_Newsletter
 * @author     
 */

class Contactlab_Subscribers_Block_Modify extends Mage_Newsletter_Block_Subscribe
{
    public function __construct() {
        $session = Mage::getSingleton('customer/session');
        if($session->isLoggedIn()){
            $this->setInputType('hidden');
            //the customer is logged: we don't need to show the corresponding input field
            //just store it in the block already so it is available as an hidden field
            // to the template
            $custid = $session->getCustomerId();
            $custmodel = Mage::getModel("customer/customer")->load($custid);
            $this->setCustomerEmail(Mage::getModel("customer/customer")->load($custid)->getEmail());
            //Mage::helper('contactlab_commons')->logInfo(print_r($custmodel->getData(),true));
        }
        else{
            $this->setInputType('text');
        } 
    }
    public function getFormActionUrl()
    {
        return $this->getUrl('contactlab/modify', array('_secure' => true));
    }

}



