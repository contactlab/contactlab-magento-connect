<?php

/**
 * Contactlab template model newsletter template compiler html.
 */
class Contactlab_Template_Model_Newsletter_Template_Compiler_Html extends Contactlab_Template_Model_Newsletter_Template_Compiler_Abstract {
    /**
     * Get compiled string.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @param Mage_Core_Model_Abstract $customer
     * @return string
     */
    protected function _getCompiledString(Mage_Newsletter_Model_Template $template,
            Mage_Core_Model_Abstract $customer) {
        // Process templte with html text, html snippets, css and no plain mode
        if ($template->hasProductImageSize()) {
            $s = explode('x', $template->getProductImageSize());
            if (count($s) > 0) {
                $this->setProductImageWidth($s[0]);
                $this->setProductImageHeight(isset($s[1]) ? $s[1] : $s[0]);
            }
        }
        return $this->_process($customer,
            $template->getTemplateText(),
            array(
                $template->getData('template_pr_html_1'),
                $template->getData('template_pr_html_2'),
                $template->getData('template_pr_html_3'),
                $template->getData('template_pr_html_4'),
                $template->getData('template_pr_html_5')
            ),
            $template->getDefaultProductSnippet(),
            $template, $template->getTemplateStyles(),
            false);
    }
}
