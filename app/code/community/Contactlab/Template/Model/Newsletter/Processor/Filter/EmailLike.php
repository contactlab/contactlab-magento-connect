<?php

/**
 * Newsletter processor filter only customers.
 * (Filter only customers newsletter subscribers)
 */
class Contactlab_Template_Model_Newsletter_Processor_Filter_EmailLike
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
            return $collection->addFieldToFilter("email", $parameters);
        } else {
            return $collection->addFieldToFilter("subscriber_email", $parameters);
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
     * Get filter name for debug.
     * @return string
     */
    public function getName() {
        return "Filter by exact email address";
    }
}
