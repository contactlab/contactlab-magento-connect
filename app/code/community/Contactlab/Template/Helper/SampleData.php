<?php

/** Template sample data helper helper. */
class Contactlab_Template_Helper_SampleData extends Mage_Core_Helper_Abstract {
    /**
     * Create sample data record.
     */
    public function createSampleData() {
        if ($handle = opendir($this->_getSampleDataPath())) {
            while (false !== ($entry = readdir($handle))) {
                if (preg_match('|\.json$|', $entry)) {
                    $this->_createSampleData($entry);
                }
            }
        }
        return $this;
    }

    /**
     * Create sample data json files.
     * @return Contactlab_Template_Helper_SampleData
     */
    public function createSampleDataJSON() {
        $this->_createSampleDataFromSubject("Subject WL");
        $this->_createSampleDataFromSubject("Subject AC");
        return $this;
    }

    /**
     * Create sample data from subject.
     * @param string $subject
     * @return Contactlab_Template_Helper_SampleData
     */
    private function _createSampleDataFromSubject($subject)
    {
        /* @var $templateCollection Mage_Newsletter_Model_Resource_Template_Collection */
        $templateCollection = Mage::getResourceModel('newsletter/template_collection');
        $templateCollection->addFieldToFilter('template_subject', $subject);
        foreach ($templateCollection as $template) {
            /* @var $template Mage_Newsletter_Model_Template */
            $this->_createSampleDataFromTemplate($template);
        }
        return $this;
    }

    /**
     * 
     * @param Mage_Newsletter_Model_Template $template
     * @return Contactlab_Template_Helper_SampleData
     */
    private function _createSampleDataFromTemplate(Mage_Newsletter_Model_Template $template)
    {
        return $this->_writeToFile(
            $this->_normalizeData($template->getData()),
            $this->_getFileNameFor($template->getTemplateSubject())
        );
    }

    /**
     * Write data.
     * @param array $data
     * @param string $file
     * @return Contactlab_Template_Helper_SampleData
     */
    private function _writeToFile(array $data, $file)
    {
        /* @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        file_put_contents($file, $helper->jsonEncode($data));
        return $this;
    }

    /**
     * Normalize data.
     * @param array $data
     * @return array
     */
    private function _normalizeData(array $data)
    {
        unset($data['template_id']);
        return $data;
    }

    /**
     * Get filename.
     * @param string $subject
     */
    private function _getFileNameFor($subject)
    {
        return $this->_getSampleDataPath()
                . DS . $this->_normalizeFileName($subject);
    }

    /**
     * Get sample data path.
     * @return string
     */
    private function _getSampleDataPath()
    {
        return realpath(__DIR__ . DS . '..' . DS . 'docs' . DS . 'example-templates');
    }

    /**
     * Normalize file name.
     * @param string $subject
     * return string
     */
    private function _normalizeFileName($subject)
    {
        return str_replace(' ', '_', strtolower($subject)) . '.json';
    }

    /**
     * Create sample data record
     * @param type $fileName
     * return Mage_Newsletter_Model_Template
     */
    private function _createSampleData($fileName)
    {
        /* @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        $data = $helper->jsonDecode(file_get_contents($this->_getSampleDataPath() . DS . $fileName));
        if ($this->_findForExisting($data['template_subject'])) {
            return false;
        }
        $template = Mage::getModel('newsletter/template');
        $template->setData($data);
        return $template->save();
    }

    /**
     * Find for existing records.
     * @param string $subject
     * @return boolean
     */
    private function _findForExisting($subject)
    {
        /* @var $templateCollection Mage_Newsletter_Model_Resource_Template_Collection */
        $templateCollection = Mage::getResourceModel('newsletter/template_collection');
        $templateCollection->addFieldToFilter('template_subject', $subject);
        foreach ($templateCollection as $template) {
            return true;
        }
        return false;
    }

}
