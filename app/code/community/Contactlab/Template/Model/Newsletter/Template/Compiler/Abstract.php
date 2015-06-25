<?php

/**
 * Contactlab template model newsletter template compiler interface.
 */
abstract class Contactlab_Template_Model_Newsletter_Template_Compiler_Abstract extends Varien_Object implements Contactlab_Template_Model_Newsletter_Template_Compiler_Interface {
    /**
     * Compile.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @param Mage_Core_Model_Abstract $customer
     * @return string
     */
    public final function compile(Mage_Newsletter_Model_Template $template, Mage_Core_Model_Abstract $customer) {
        return $this->_getCompiledString($template, $customer);
    }

    /**
     * Get compiled string.
     *
     * @param Mage_Newsletter_Model_Template $template
     * @param Mage_Core_Model_Abstract $customer
     * @return string
     */
    protected abstract function _getCompiledString(Mage_Newsletter_Model_Template $template,
            Mage_Core_Model_Abstract $customer);

    /**
     * Process template.
     *
     * @param Mage_Core_Model_Abstract $customer
     * @param string $templateText
     * @param array $snippets
     * @param int $defaultProductSnippet
     * @param string $templateStyles = ""
     * @param Contactlab_Template_Model_Newsletter_Template $templateObject
     * @return void
     */
    protected function _process(Mage_Core_Model_Abstract $customer,
            $templateText, array $snippets, $defaultProductSnippet,
            Contactlab_Template_Model_Newsletter_Template $templateObject,
            $templateStyles = "", $isPlain = true) {

        /* @var $template Mage_Core_Model_Email_Template */
        $template = Mage::getModel('core/email_template');
        $template->setType($isPlain
                ? Mage_Core_Model_Template::TYPE_TEXT
                : Mage_Core_Model_Template::TYPE_HTML);
        $template->setTemplateText($templateText);
        $template->setTemplateStyles($templateStyles);

        $productExplode = explode(',', $customer->getProductIds());
        $customer->setProductsNumber(count($productExplode));

        /* @var $subscriber Mage_Newsletter_Model_Subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber');

        /* @var $realCustomer Mage_Customer_Model_Customer */
        $realCustomer = Mage::getModel('customer/customer')->load($customer->getCustomerId());

        $subscriber->loadByCustomer($realCustomer);
        if ($subscriber->getSubscriberId()) {
            $customer->setUnsubscriptionLink($subscriber->getUnsubscriptionLink());
        }
        
        $counter = 0;
        $productsObject = new Varien_Object();

        // Iterate products and calculate template
        foreach ($productExplode as $productItem) {
            $item = explode('|', $productItem);
            if ($productItem === '') {
                continue;
            }
            $productQty = $item[1];
            $productId = $item[0];
            /* @var $products Mage_Catalog_Model_Resource_Product_Collection */
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addIdFilter(array($productId))->load();
            foreach ($products as $product) {}
            if (isset($snippets[$counter])) {
                $productTemplateText = $snippets[$counter];
            } else {
                $productTemplateText = $snippets[$defaultProductSnippet];
            }
            /* @var $productTemplate Mage_Core_Model_Email_Template */
            $productTemplate = Mage::getModel('core/email_template');
            $productTemplate->setType($isPlain ? Mage_Core_Model_Template::TYPE_TEXT : Mage_Core_Model_Template::TYPE_HTML);
            $productTemplate->setTemplateText($productTemplateText);
            $product->setQty($productQty);
            if ($this->hasProductImageWidth()) {
                $product->setThumbnail($product->getThumbnailUrl($this->getProductImageWidth(), $this->getProductImageHeight()));
            }
            $qty = $this->formatQty($product);
            $productTemplateResult = $productTemplate->getProcessedTemplate(
                array('product' => $product, 'qty' => $qty,
                    'price' => $templateObject->getPriceFor($product, $item, $this->getStoreId())));
            $counter++;
            $productsObject->setData('product' . $counter, $productTemplateResult);
        }
        return $template->getProcessedTemplate(array(
            'subscriber' => $customer,
            'products' => $productsObject
        ));
    }

    /**
     * Format qty.
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function formatQty(Mage_Catalog_Model_Product $product) {
        /* @var $stock Mage_Cataloginventory_Model_Stock_Item */
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        if ($product->hasQty() && $stock->getIsQtyDecimal()) {
            $qty = Zend_Locale_Format::toNumber($product->getQty(),
                                array('precision' => 2));
        } else if ($product->hasQty() && !$stock->getIsQtyDecimal()) {
            $qty = intval($product->getQty());
        } else {
            $qty = '';
        }
        $product->setQty($qty);
        return $qty;
    }
}
