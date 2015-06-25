<?php

/**
 * Contactlab template model newsletter template compiler text.
 */
class Contactlab_Template_Model_Newsletter_Template_Compiler_Text extends Contactlab_Template_Model_Newsletter_Template_Compiler_Abstract {
    /**
     * Get compiled string.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @param Mage_Core_Model_Abstract $customer
     * @return string
     */
    protected function _getCompiledString(Mage_Newsletter_Model_Template $template,
            Mage_Core_Model_Abstract $customer) {

        // Process templte with text, text snippets, no css and plain mode
        return $this->_process($customer,
            $template->getTemplateTextPlain(),
            array(
                $template->getData('template_pr_txt_1'),
                $template->getData('template_pr_txt_2'),
                $template->getData('template_pr_txt_3'),
                $template->getData('template_pr_txt_4'),
                $template->getData('template_pr_txt_5')
            ),
            $template->getDefaultProductSnippet(), $template);
    }
}
