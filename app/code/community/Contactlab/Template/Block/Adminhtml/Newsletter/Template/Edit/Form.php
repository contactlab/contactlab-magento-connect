<?php

/**
 * Adminhtml Newsletter Template Edit Form Block
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Edit_Form extends Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
     */

    protected function _prepareForm() {
        $returnValue = parent::_prepareForm();

        /** @var $model Contactlab_Template_Model_Newsletter_Template */
        $model = $this->getModel();
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();
        $yesNo = Mage::getModel('adminhtml/system_config_source_yesno');
        $stores = $this->getStoresOptions();

        /** @var $fieldSet Varien_Data_Form_Element_Fieldset */
        $fieldSet = $form->getElement('base_fieldset');

        // Remove fields.
        $fieldSet->removeField('text');
        $fieldSet->removeField('template_styles');


        $fieldSet->addField('enable_xml_delivery', 'select', array(
            'required' => false,
            'name'     => 'enable_xml_delivery',
            'default'  => '1',
            'label'    => $h->__('Use XML Delivery'),
            'values'   => $yesNo->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getEnableXmlDelivery() : 1));

        $fieldSet->addField('store_id', 'select', array(
            'required' => false,
            'name'     => 'store_id',
            'label'    => Mage::helper('adminhtml')->__('Store View'),
            'values'   => $stores,
            'value'    => $model->getStoreId()));


        $types = Mage::getModel('contactlab_template/system_config_source_template_type');

        $fieldSet->addField('reply_to', 'text', array(
            'required' => false,
            'name'     => 'reply_to',
            'label'    => $h->__('Reply to'),
            'value'    => $model->getId() !== null ? $model->getReplyTo() : ''));
        $fieldSet->addField('template_type_id', 'select', array(
            'required' => false,
            'name'     => 'template_type_id',
            'values'   => $types->toOptionArray(),
            'label'    => $h->__('Template type'),
            'value'    => $model->getId() !== null ? $model->getTemplateTypeId() : ''));
        $fieldSet->addField('text', 'textarea', array(
            'required' => true,
            'name'     => 'text',
            'label'    => $h->__('Template (html format)'),
            'value'    => $model->getTemplateText()));
        $fieldSet->addField('template_text_plain', 'textarea', array(
            'required' => true,
            'name'     => 'template_text_plain',
            'label'    => $h->__('Template (text format)'),
            'value'    => $model->getTemplateTextPlain()));
        $formats = Mage::getModel('contactlab_template/system_config_source_template_format');
        $fieldSet->addField('flg_html_txt', 'select', array(
            'required' => true,
            'name'     => 'flg_html_txt',
            'label'    => $h->__('Email format type'),
            'title'    => $h->__('Text, html or both'),
            'values'   => $formats->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getFlgHtmlTxt() : 'B'));

        $fieldSet->addField('template_styles', 'textarea', array(
            'name'          =>'styles',
            'label'         => Mage::helper('newsletter')->__('Template Styles'),
            'container_id'  => 'field_template_styles',
            'value'         => $model->getTemplateStyles()
        ));
        $fieldSet->addField('is_test_mode', 'select', array(
            'name'      => 'is_test_mode',
            'label'     => Mage::helper('contactlab_template')->__('Send to test recipients'),
            'title'     => Mage::helper('contactlab_template')->__('Send to test recipients'),
            'options'   => array(
                1 => Mage::helper('adminhtml')->__('Yes'),
                0 => Mage::helper('adminhtml')->__('No')
            ),
            'value'     => $model->getIsTestMode(),
        ));


        $this->_addProductFieldSet();
        $this->_addCustomerFilterOptions();
        $this->_addCronFieldSet();

        return $returnValue;
    }


    /**
     * Add product fields fileset
     */
    private function _addProductFieldSet() {
        /** @var $model Contactlab_Template_Model_Newsletter_Template */
        $model = $this->getModel();
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();

        $fieldSet = $form->addFieldset('product_fieldset', array(
            'legend' => $h->__('Product templates'),
            'class'  => 'fieldset-wide'
        ));

        foreach (range(1, 5) as $i) {
            $fieldSet->addField("template_pr_txt_$i", 'textarea', array(
                'required' => false,
                'name'     => "template_pr_txt_$i",
                'label'    => $h->__("Product template nr %d (text)", $i),
                'title'    => $h->__("Template nr %d for product (text/plain)", $i),
                'value'    => $model->getId() !== null ? $model->getData("template_pr_txt_$i") : ''));
            $fieldSet->addField("template_pr_html_$i", 'textarea', array(
                'required' => false,
                'name'     => "template_pr_html_$i",
                'label'    => $h->__("Product template nr %d (html)", $i),
                'title'    => $h->__("Template nr %d for product (html)", $i),
                'value'    => $model->getId() !== null ? $model->getData("template_pr_html_$i") : ''));
        }

        $fieldSet->addField('default_product_snippet', 'text', array(
            'required' => false,
            'name'     => 'default_product_snippet',
            'label'    => $h->__('Default product snippet (1-5)'),
            'title'    => $h->__('Default product snippet number'),
            'value'    => $model->getId() !== null ? $model->getDefaultProductSnippet() : ''));

        $fieldSet->addField('product_image_size', 'text', array(
            'required' => false,
            'name'     => 'product_image_size',
            'label'    => $h->__('Product image size (width x height)'),
            'title'    => $h->__('Product image size (width x height)'),
            'value'    => $model->getId() !== null ? $model->getProductImageSize() : ''));
    }


    /**
     * Add cron fields fileset
     */
    private function _addCronFieldSet() {
        /** @var $model Contactlab_Template_Model_Newsletter_Template */
        $model = $this->getModel();
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();
        $yesNo = Mage::getModel('adminhtml/system_config_source_yesno');

        $fieldSet = $form->addFieldset('cron_fieldset', array(
            'legend' => $h->__('Cron Information'),
            'class'  => 'fieldset-narrow'
        ));

        $fieldSet->addField('is_cron_enabled', 'select', array(
            'label'      => $h->__('Activate for Cron execution'),
            'title'      => $h->__('Does the template is active for Cron?'),
            'name'       => 'is_cron_enabled',
            'values'   => $yesNo->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getIsCronEnabled() : 0));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldSet->addField('cron_date_range_start', 'date', array(
            'label'        => $h->__('Enabled start date'),
            'title'        => $h->__('Start date for Cron'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
            'required'     => false,
            'name'         => 'cron_date_range_start',
            'value'        => $model->getId() !== null ? $model->getCronDateRangeStart() : ''));
        $fieldSet->addField('cron_date_range_end', 'date', array(
            'label'        => $h->__('Enabled end date'),
            'title'        => $h->__('End date for Cron'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
            'required'     => false,
            'name'         => 'cron_date_range_end',
            'value'        => $model->getId() !== null ? $model->getCronDateRangeEnd() : ''));
        $fieldSet->addField('queue_delay_time', 'text', array(
            'label'    => $h->__('Queue delay time'),
            'title'    => $h->__('Optional queue delay time'),
            'required' => false,
            'name'     => 'queue_delay_time',
            'value'    => $model->getId() !== null ? $model->getQueueDelayTime() : 0));

        $fieldSet->addField('priority', 'text', array(
            'required' => true,
            'name'     => 'priority',
            'label'    => $h->__('Priority'),
            'value'    => $model->getId() !== null ? $model->getPriority() : 0));
    }

    /**
     * Add customer filter options.
     *
     * @return $this
     */
    public function _addCustomerFilterOptions() {
        /** @var $model Contactlab_Template_Model_Newsletter_Template */
        $model = $this->getModel();

        /* @var $h Contactlab_Template_Helper_Data */
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();
        $andOr = Mage::getModel('contactlab_template/system_config_source_andOr');

        $fieldSet = $form->addFieldset('customer_filter_fieldset', array(
            'legend' => $h->__('Customer filter options'),
            'class'  => 'fieldset-narrow'
        ));

        $fieldSet->addField('min_minutes_from_last_update', 'text', array(
            'required' => false,
            'class' => 'validate-not-negative-number validate-digits validate-is-min-of-range',
            'label' => $h->__('Minimum number of minutes'),
            'title' => $h->__('Minimum number of minutes'),
            'name' => 'min_minutes_from_last_update',
            'value'    => $model->getId() !== null ? $model->getMinMinutesFromLastUpdate() : ''));

        $fieldSet->addField('max_minutes_from_last_update', 'text', array(
            'required' => false,
            'class' => 'validate-not-negative-number validate-digits validate-is-max-of-range',
            'label' => $h->__('Maximum number of minutes'),
            'title' => $h->__('Maximum number of minutes'),
            'name' => 'max_minutes_from_last_update',
            'value'    => $model->getId() !== null ? $model->getMaxMinutesFromLastUpdate() : ''));

        $fieldSet->addField('min_value', 'text', array(
            'required' => false,
            'class' => 'validate-number validate-not-negative-number validate-is-min-of-range',
            'label' => $h->__('Minimum value'),
            'title' => $h->__('Minimum value'),
            'comment' => '[EUR]',
            'name' => 'min_value',
            'value'    => $model->getId() !== null ? $h->formatPrice($model->getMinValue()) : ''));

        $fieldSet->addField('max_value', 'text', array(
            'required' => false,
            'class' => 'validate-number validate-not-negative-number validate-is-max-of-range',
            'label' => $h->__('Maximum value'),
            'title' => $h->__('Maximum value'),
            'name' => 'max_value',
            'value'    => $model->getId() !== null ? $h->formatPrice($model->getMaxValue()) : ''));

        $fieldSet->addField('min_products', 'text', array(
            'required' => false,
            'class' => 'validate-greater-than-zero validate-digits validate-is-min-of-range',
            'label' => $h->__('Minimum number of products'),
            'title' => $h->__('Minimum number of products'),
            'name' => 'min_products',
            'value'    => $model->getId() !== null ? $model->getMinProducts() : ''));

        $fieldSet->addField('max_products', 'text', array(
            'required' => false,
            'class' => 'validate-greater-than-zero validate-digits validate-is-max-of-range',
            'label' => $h->__('Maximum number of products'),
            'title' => $h->__('Maximum number of products'),
            'name' => 'max_products',
            'value'    => $model->getId() !== null ? $model->getMaxProducts() : ''));

        $fieldSet->addField('and_or', 'select', array(
            'required' => true,
            'label' => $h->__('And/or condition values'),
            'title' => $h->__('And/or condition values'),
            'name' => 'and_or',
            'values'   => $andOr->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getAndOr() : 'AND'));

        return $this;
    }

    /**
     * Get stores options.
     * @return array
     */
    private function getStoresOptions()
    {
        /* @var $h Contactlab_Template_Helper_Data */
        $h = Mage::helper('contactlab_template');
        $stores = Mage::getModel('adminhtml/system_config_source_store')->toOptionArray();
        $stores = array_reverse($stores);
        $stores['none'] = $h->__('Any');
        $stores = array_reverse($stores);
        return $stores;
    }
}
