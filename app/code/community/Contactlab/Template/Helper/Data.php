<?php

/** Template helper. */
class Contactlab_Template_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Scan for template to be sent.
     *
     * @param string $storeId = 0
     * @return array
     */
    public function scan($storeId = 0) {
        $rv = array();
        $templates = Mage::getResourceModel("newsletter/template_collection")
            ->loadActiveTemplatesForCron();
        // $helper->logDebug("Scan " . $templates->count() . " templates found");
        foreach ($templates as $template) {
            $r = $template->processTemplateQueue($storeId);
            if ($r) {
                $rv[$template->getTemplateCode()] = $r;
            }
        }
        return $rv;
    }


    /**
     * Get available stores.
     * @return Varien_Data_Collection
     */
    public function getAvailableStores() {
        $stores = Mage::getModel('core/store')->getCollection()
            ->setLoadDefault(true);
        $stores->getSelect()->order('store_id');
        return $stores;
    }

    /**
     * Is this store enabled for sending in cron?
     * @param Mage_Core_Model_Store $store
     * @return void
     */
    public function isStoreEnabled(Mage_Core_Model_Store $store) {
        return Mage::getStoreConfigFlag('contactlab_template/global/enabled', $store);
    }


    /**
     * Check newsletter queue report.
     *
     * @param Contactlab_Commons_Model_Task $task
     * @param Contactlab_Commons_Model_Task $parentTask
     * @param string $xmlFile
     * @param string $storeId
     * @return string
     */
    public function checkNewsletterQueueReport(Contactlab_Commons_Model_Task $task,
            Contactlab_Commons_Model_Task $parentTask, $xmlFile, $storeId, $queueId) {
        /* @var $checker Contactlab_Template_Model_Newsletter_XmlDelivery_Check */
        $checker = Mage::getModel('contactlab_template/newsletter_xmlDelivery_check');
        $checker->setTask($task)
                ->setParentTask($parentTask)
                ->setXmlFile($xmlFile)
                ->setStoreId($storeId)
                ->setQueueId($queueId);
        return $checker->doCheck();
    }

    /**
     * Format price with 2 decimal digits.
     * @param string $price
     * @return string
     */
    public function formatPrice($price) {
        if (trim($price) === '') {
            return '';
        }
        return money_format("%!.2n", $price);
    }
}
