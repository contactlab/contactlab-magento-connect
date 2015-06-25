<?php

/**
 * Newsletter processor filter only customers.
 * (Filter only customers newsletter subscribers)
 */
class Contactlab_Template_Model_Newsletter_Processor_Filter_TestMode
        extends Contactlab_Template_Model_Newsletter_Processor_Filter_Abstract {

    /**
     * Apply filter to use only customers.
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $parameters = array()
     * @return A collection
     */
    public function applyFilter(Varien_Data_Collection_Db $collection, $parameters = array()) {
        if ($this->isCustomerCollection($collection)) {
            return $collection->getSelect()->where("e.email in (?)", $this->getTestSubscribersMail());
        } else {
            return $collection->getSelect()->where("main_table.subscriber_email in (?)", $this->getTestSubscribersMail());
        }
    }


    /**
     * Do run in test mode?
     *
     * @return false
     */
    public function doRunInTestMode() {
        return true;
    }

    /**
     * Get test subscribers mail.
     *
     * @return array
     */
    public function getTestSubscribersMail() {
        $rv = explode(',', Mage::getStoreConfig('contactlab_template/global/test_recipients', $this->getStoreId()));
        if (count($rv) == 0) {
            throw new Zend_Exception("Could not send test email, empty \"Test Recipients\" configuration!");
        }
        return $rv;
    }

    /**
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter only test email addresses";
    }
}
