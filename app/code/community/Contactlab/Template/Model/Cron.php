<?php

/**
 * Contactlab template model cron.
 * Cron model called from cronjob task queue.
 */
class Contactlab_Template_Model_Cron {

    /**
     * Scan for template to be sent.
     * @param int|string $storeId
     */
    public function scan($schedule = -1, $storeId = -1) {
        $this->logCronCall('scan', $storeId);

        /** @var $h Contactlab_Template_Helper_Data */
        $h = Mage::helper('contactlab_template');
        foreach ($h->getAvailableStores() as $store) {
            /** @var $store Mage_Core_Model_Store */
            if (!$h->isStoreEnabled($store)) {
                continue;
            }
            // If a store id has been passed as argument,
            // check before acl helper.
            try {
                Mage::helper('contactlab_template')->scan($store->getStoreId());
            } catch (Zend_Exception $e) {
                Mage::helper('contactlab_commons')->logEmerg($e);
            }
        }
    }

    /**
     * Log function call.
     * @param String $functionName
     * @param bool|String $storeId
     */
    public function logCronCall($functionName, $storeId = false)
    {
        Mage::helper('contactlab_commons')
            ->logCronCall(
                "Contactlab_Template_Model_Cron::$functionName", $storeId
            );
    }
}
