<?php


/** Newsletter templates collection. */
class Contactlab_Template_Model_Resource_Newsletter_Template_Collection extends Mage_Newsletter_Model_Resource_Template_Collection {

    /**
     * Load active templates for cron.
     *
     * @return collection
     */
    public function loadActiveTemplatesForCron() {
        $this->getSelect()->where('(cron_date_range_start is null or cron_date_range_start <= date(now()))');
        $this->getSelect()->where('(cron_date_range_start is null or cron_date_range_end >= date(now()))');
        $this->getSelect()->where('is_test_mode = 0');
        $this->getSelect()->order('priority desc');
        return $this->addFieldToFilter('is_cron_enabled', 1);
    }
}
