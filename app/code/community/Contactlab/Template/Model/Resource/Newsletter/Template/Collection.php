<?php


/** Newsletter templates collection. */
class Contactlab_Template_Model_Resource_Newsletter_Template_Collection extends Mage_Newsletter_Model_Resource_Template_Collection {

    /**
     * Load active templates for cron.
     *
     * @param int $storeId
     * @param bool $excludeTest Exclude test mode templates
     * @return Contactlab_Template_Model_Resource_Newsletter_Template_Collection
     */
    public function loadActiveTemplatesForCron($storeId = -1, $excludeTest = true) {
        $this->getSelect()->where('(cron_date_range_start is null or cron_date_range_start <= date(now()))');
        $this->getSelect()->where('(cron_date_range_start is null or cron_date_range_end >= date(now()))');
        if ($excludeTest) {
            $this->getSelect()->where('is_test_mode = 0');
        }
        if ($storeId >= 0) {
            $this->getSelect()->where('store_id is null or store_id = ?', intval($storeId));
        }
        $this->getSelect()->order('priority desc');
        $this->addFieldToFilter('is_cron_enabled', 1);

        return $this;
    }
}
