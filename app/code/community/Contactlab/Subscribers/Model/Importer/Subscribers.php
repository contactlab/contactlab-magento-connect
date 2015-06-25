<?php

/**
 * Export subscribers.
 * @method Contactlab_Commons_Model_Task getTask()
 */
class Contactlab_Subscribers_Model_Importer_Subscribers extends Contactlab_Commons_Model_Importer_Abstract {
    /**
     * Get file name.
     */
    protected function getFileName() {
        return $this->getTask()->getConfig("contactlab_subscribers/global/import_filename");
    }

    /**
     * Is the import enabled?
     */
    protected function isEnabled() {
        return Mage::helper("contactlab_subscribers")
                ->isEnabledContactlab2Magento($this->getTask());
    }

    /**
     * Import xml object. 
     */
    protected function importXml(SimpleXMLElement $xml) {
        $this->setWebFormCode(
            $this->getTask()->getConfig('contactlab_subscribers/global/web_form_code')
        );
        /* @var $h Contactlab_Subscribers_Helper_Data */
        $h = Mage::helper("contactlab_subscribers");
        $h->setSkipUnsubscribeSoapCall();
        $counterSuccess = 0;
        $counterError = 0;
        $counterIgnored = 0;
        foreach ($xml->RECORDELEMENT as $item) {
            if (!$this->_filter($item)) {
                // CATEGORY_DESC
                $counterIgnored++;
                continue;
            }
            if ($h->unsubscribe(
                    $this->getTask(),
                    (string) $item->EVENT_DETAILS,
                    floatval((string) $item->ENTITY_ID),
                    (string) $item->EVENT_DATE)) {
                $counterSuccess++;
            } else {
                $counterError++;
            }
        }
        if ($counterError + $counterSuccess + $counterIgnored == 0) {
            $this->getTask()->addEvent("No items in xml file");
        } else {
             if ($counterError > 0) {
                 $this->getTask()->addEvent("$counterError items imported with error", true);
             }
             if ($counterSuccess > 0) {
                 $this->getTask()->addEvent("$counterSuccess items imported successfully");
             }
             if ($counterIgnored > 0) {
                 $this->getTask()->addEvent("$counterIgnored items ignored");
             }
        }
    }

    /**
     * Filter the row.
     * @param SimpleXMLElement $item
     * @return boolean: true if row is ok, false if is to skip
     */
    private function _filter(SimpleXMLElement $item)
    {
        // Check event code ('o')
        if ((string) $item->EVENT_CODE !== 'o') {
            return false;
        }
        // Check WebForCode
        if ((string) $item->CATEGORY_DESC !== $this->getWebFormCode()) {
            return false;
        }
        return true;
    }
}
