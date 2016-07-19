<?php


/**
 * Newsletter modify block.
 *
 * @method setInputType($value)
 * @method setCustomerEmail($value)
 * @method String getInputType()
 * @method String getCustomerEmail()
 *
 * @category   Contactlab
 * @package    Contactlab_Newsletter
 * @author
 */
class Contactlab_Subscribers_Block_Newsletter_Subscribers_Modify extends Mage_Newsletter_Block_Subscribe
{
	
	protected function _toHtml()
	{
		if(Mage::getStoreConfig('contactlab_subscribers/newsletter/enable'))
		{
			return parent::_toHtml();
		}
	}
	
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $this->setInputType('hidden');
            // The customer is logged: we don't need to show the corresponding input field
            // just store it in the block already so it is available as an hidden field to the template
            $customerId = $session->getCustomerId();
            $this->setCustomerEmail(Mage::getModel("customer/customer")->load($customerId)->getEmail());
        } else {
            $this->setInputType('text');
        }
    }

    /**
     * Get form action url.
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('contactlab_subscribers/modify', array('_secure' => true));
    }
}
