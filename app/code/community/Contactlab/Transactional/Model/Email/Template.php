<?php

/**
 * Transactional email data helper.
 */
class Contactlab_Transactional_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Retrieve mail object instance
     * @return Contactlab_Transactional_Model_Zend_Mail
     */
    public function getMail() {
        if (is_null($this->_mail)) {
            if ($this->_isEnabled()) {
                $this->_mail = Mage::getModel('contactlab_transactional/zend_mail', 'utf-8');
            } else {
                $this->_mail = new Zend_Mail('utf-8');
            }
        }
        return $this->_mail;
    }

    /**
     * Is sending with Smart Realy abled?
     */
    private function _isEnabled() {
        return Mage::getStoreConfigFlag("contactlab_transactional/global/enabled");
    }

    /**
     * Load default email template from locale translate
     *
     * @param string $templateId
     * @param string $locale
     */
    public function loadDefault($templateId, $locale = null) {
        $rv = parent::loadDefault($templateId, $locale);
        if ($this->_isEnabled()) {
            $code = $this->_getHtmlFileName($templateId);
            $this->getMail()->setTemplateCode($code);
            $this->getMail()->setTemplateId($this->getTemplateType());
        }
        return $rv;
    }

    private function _getHtmlFileName($templateId) {
        $defaultTemplates = self::getDefaultTemplates();
        if (!isset($defaultTemplates[$templateId])) {
            if ($this->hasTemplateCode()) {
                return $this->getTemplateCode();
            }
            return $this->getTemplateId();
        }
        $data = &$defaultTemplates[$templateId];
        return str_replace('.html', '', $data['file']);
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field = null) {
        $rv = parent::load($id, $field);
        if ($this->_isEnabled()) {
            $code = $this->getTemplateId();
            if (true) {
                $code = $this->_getHtmlFileName($id);
            } else if ($this->hasTemplateCode()) {
                $code = $this->getTemplateCode();
            }
            $this->getMail()->setTemplateCode($code);
            $this->getMail()->setTemplateId($this->getTemplateType());
        }
        return $rv;
    }
}
