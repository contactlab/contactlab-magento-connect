<?php

require_once('Mage/Adminhtml/controllers/Newsletter/QueueController.php');

/**
 * Manage Newsletter Template Controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Contactlab_Template_Adminhtml_Newsletter_QueueController
        extends Mage_Adminhtml_Newsletter_QueueController {

    public function editAction() {
        $templateId = $this->getRequest()->getParam('template_id');
        if ($templateId) {
            $template = Mage::getModel('newsletter/template')->load($templateId);
            if ($template->getEnableXmlDelivery()) {
                $h = Mage::helper('contactlab_template');
                foreach ($h->getAvailableStores() as $store) {
                    if (!$h->isStoreEnabled($store)) {
                        continue;
                    }
                    try {
                        $template->setDontRunNow(true);
                        $template->processTemplateQueue($store->getStoreId());
                    } catch (Zend_Exception $e) {
                        Mage::helper('contactlab_commons')->logEmerg($e);
                    }
                }

                $this->_redirect('adminhtml/contactlab_commons_tasks');
            } else {
                return parent::editAction();
            }
        }
    }
}
