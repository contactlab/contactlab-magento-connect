<?php

/** Template types controller. */
class Contactlab_Template_Adminhtml_TemplateController extends Mage_Adminhtml_Controller_Action {

    /**
     * Scan for template in cron.
     */
    public function scanAction() {
		$session = Mage::getSingleton('adminhtml/session');
        $h = Mage::helper('contactlab_template');
        try {
            foreach ($h->getAvailableStores() as $store) {
                if (!$h->isStoreEnabled($store)) {
                    continue;
                }
                $this->_scanByStore($store->getStoreId(),
                        $this->getRequest()->getParam('address'));
            }
        } catch (Zend_Exception $e) {
            Mage::helper('contactlab_commons')->logEmerg($e);
			$session->addError($e->getMessage());
        }
        $this->_redirect('contactlab_commons/adminhtml_tasks/',
                array('address' => $this->getRequest()->getParam('address')));
    }

    /**
     * Scan templates by store.
     *
     * @param string $storeId
     * @param string $debugAddress
     */
    private function _scanByStore($storeId, $debugAddress) {
		$session = Mage::getSingleton('adminhtml/session');
        $rv = Mage::helper('contactlab_template')->scan($storeId, $debugAddress);
        if (is_array($rv)) {
            foreach ($rv as $k => $v) {
    			$session->addSuccess(sprintf("%s: %s", $k, $v));
            }
        }
    }
}
