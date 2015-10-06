<?php

/**
 * Template helper.
 * Manages template scan.
 */
class Contactlab_Template_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Scan for template to be sent.
     *
     * @param int|string $storeId = 0
     * @param string $debugAddress
     * @param bool $excludeTest Exclude test mode templates
     * @return array
     */
    public function scan($storeId = 0, $debugAddress = null, $excludeTest = true) {
        $rv = array();
        /* @var $templates Mage_Newsletter_Model_Resource_Template_Collection */
        $templates = Mage::getResourceModel("newsletter/template_collection")
            ->loadActiveTemplatesForCron($storeId, $excludeTest);

        // $helper->logDebug("Scan " . $templates->count() . " templates found");
        $info = array();
        if ($templates->count() === 0) {
            $this->addSessionWarning("No <strong>templates</strong> found.");
        } else {
            $message = "Scanning <strong>" . $templates->count() . " templates</strong> found: ";
            /** @var $template Contactlab_Template_Model_Newsletter_Template */
            foreach ($templates as $template) {
                /* @var $template Contactlab_Template_Model_Newsletter_Template */
                $message .= $template->getTemplateSubject() . ", ";
                $template->setDebugAddress($debugAddress);
                $r = $template->processTemplateQueue($storeId);
                if ($r) {
                    $rv[$template->getTemplateCode()] = $r;
                }
                if ($template->hasDebugInfo()) {
                    $info[] = $template->getDebugInfo();
                }
            }
            $message = preg_replace("!, $!", ".", $message);
            $this->addSessionMessage($message);
            foreach ($info as $i) {
                if (is_array($i)) {
                    foreach ($i as $l) {
                        $this->addDebugInfo($l);
                    }
                } else {
                    $this->addDebugInfo($i);
                }
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
     * @return boolean
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
     * @param int $queueId
     * @return string
     * @throws Exception
     * @throws Zend_Exception
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

    /**
     * Add session message.
     * @param String $message
     */
    public function addSessionMessage($message) {
        /* @var $session Mage_Adminhtml_Model_Session */
        if ($this->shouldSendMessages()) {
            $session = Mage::getSingleton("adminhtml/session");
            $session->addSuccess($message);
        }
    }

    /**
     * Add session message.
     * @param String $message
     */
    public function addSessionWarning($message) {
        /* @var $session Mage_Adminhtml_Model_Session */
        if ($this->shouldSendMessages()) {
            $session = Mage::getSingleton("adminhtml/session");
            $session->addWarning($message);
        }
    }

    /**
     * Only for apache.
     * @return boolean
     */
    public function shouldSendMessages() {
        return php_sapi_name() !== 'cli';
    }

    /**
     * Add debug info.
     * @param Mage_Core_Model_Message_Abstract $l
     */
    public function addDebugInfo(Mage_Core_Model_Message_Abstract $l) {
        if ($this->shouldSendMessages()) {
            /* @var $session Mage_Adminhtml_Model_Session */
            $session = Mage::getSingleton("adminhtml/session");
            $session->addMessage($l);
        }
    }

}
