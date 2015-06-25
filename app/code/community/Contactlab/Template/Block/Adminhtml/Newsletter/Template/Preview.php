<?php
/**
 * Contactlab template block adminhtml newsletter template preview block.
 *
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Preview
        extends Mage_Adminhtml_Block_Newsletter_Template_Preview {

    /**
     * To html.
     *
     * @return string
     */
    protected function _toHtml() {
        /* @var $template Mage_Newsletter_Model_Template */
        $template = Mage::getModel('newsletter/template');
        $this->setTemplateTypeCode('wishlist');

        if ($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
            if ($template->getEnableXmlDelivery()) {
                $this->setTemplateTypeCode(strtolower($template->getTemplateTypeModel()->getTemplateTypeCode()));
                return $this->_previewXmlDelivery($template);
            } else {
                return parent::_toHtml();
            }
        } else {
            return parent::_toHtml();
        }
    }

    /**
     * Get test collection.
     *
     * @return collection
     */
    public function getTestCollection() {
        $collection = Mage::getResourceModel('newsletter/subscriber_collection');
        $this->setStoreId($this->getRequest()->getParam('store_id'));
        Mage::getModel('contactlab_template/newsletter_processor_filter_testMode')
                ->setStoreId($this->getStoreId())->applyFilter($collection);
        if ($this->getTemplateTypeCode() == 'wishlist' || $this->getTemplateTypeCode() == 'cart') {
            Mage::getModel('contactlab_template/newsletter_processor_filter_'
                    . $this->getTemplateTypeCode() . '_products')
                    ->setStoreId($this->getStoreId())->applyFilter($collection);
        }
        Mage::getSingleton('newsletter/queue')->addCustomerInfo($collection, 'main_table');

        return $collection;
    }

    /**
     * Preview xml delivery.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @return string
     */
    private function _previewXmlDelivery(Mage_Newsletter_Model_Template $template) {
        foreach ($this->getTestCollection() as $item) {
            $rv = <<<EOT
            <style type="text/css">.section-title {
                color: #128;
                font-size: 15px;
                margin: 0;
                padding: 0;
            }</style>
            <h1 class="section-title">Html template</h1>
EOT;

            $compiler = Mage::getSingleton("contactlab_template/newsletter_template_compiler_html");
            $rv .= $compiler
                ->setStoreId(0)
                ->compile($template, $item);

            $rv .= "<h1 class=\"section-title\">Text template</h1><pre style=\"font-family: monospace\">";

            $compiler = Mage::getSingleton("contactlab_template/newsletter_template_compiler_text");
            $rv .= $compiler
                ->setStoreId(0)
                ->compile($template, $item);
            $rv .= "</pre>";
            return $rv;
        }
    }
}
