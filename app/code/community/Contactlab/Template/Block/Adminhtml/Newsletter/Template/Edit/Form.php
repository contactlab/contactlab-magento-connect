<?php

/**
 * Adminhtml Newsletter Template Edit Form Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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

        $model = $this->getModel();
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();
        $yesNo = Mage::getModel('adminhtml/system_config_source_yesno');

        $fieldset = $form->getElement('base_fieldset');

        // Remove fields.
        $fieldset->removeField('text');
        $fieldset->removeField('template_styles');


        $fieldset->addField('enable_xml_delivery', 'select', array(
            'required' => false,
            'name'     => 'enable_xml_delivery',
            'default'  => '1',
            'label'    => $h->__('Use XML Delivery'),
            'values'   => $yesNo->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getEnableXmlDelivery() : 1));


        $types = Mage::getModel('contactlab_template/system_config_source_template_type');

        $fieldset->addField('reply_to', 'text', array(
            'required' => false,
            'name'     => 'reply_to',
            'label'    => $h->__('Reply to'),
            'value'    => $model->getId() !== null ? $model->getReplyTo() : ''));
        $fieldset->addField('template_type_id', 'select', array(
            'required' => false,
            'name'     => 'template_type_id',
            'values'   => $types->toOptionArray(),
            'label'    => $h->__('Template type'),
            'value'    => $model->getId() !== null ? $model->getTemplateTypeId() : ''));
        $fieldset->addField('text', 'textarea', array(
            'required' => true,
            'name'     => 'text',
            'label'    => $h->__('Template (html format)'),
            'value'    => $model->getTemplateText()));
        $fieldset->addField('template_text_plain', 'textarea', array(
            'required' => true,
            'name'     => 'template_text_plain',
            'label'    => $h->__('Template (text format)'),
            'value'    => $model->getTemplateTextPlain()));
        $formats = Mage::getModel('contactlab_template/system_config_source_template_format');
        $fieldset->addField('flg_html_txt', 'select', array(
            'required' => true,
            'name'     => 'flg_html_txt',
            'label'    => $h->__('Email format type'),
            'title'    => $h->__('Text, html or both'),
            'values'   => $formats->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getFlgHtmlTxt() : 'B'));

        $fieldset->addField('template_styles', 'textarea', array(
            'name'          =>'styles',
            'label'         => Mage::helper('newsletter')->__('Template Styles'),
            'container_id'  => 'field_template_styles',
            'value'         => $model->getTemplateStyles()
        ));
        $fieldset->addField('is_test_mode', 'select', array(
            'name'      => 'is_test_mode',
            'label'     => Mage::helper('contactlab_template')->__('Send to test recipients'),
            'title'     => Mage::helper('contactlab_template')->__('Send to test recipients'),
            'options'   => array(
                1 => Mage::helper('adminhtml')->__('Yes'),
                0 => Mage::helper('adminhtml')->__('No')
            ),
            'value'     => $model->getIsTestMode(),
        ));


        $this->_addProductFieldset();
        $this->_addCustomerFilterOptions();
        $this->_addCronFieldset();

        return $returnValue;
    }


    /**
     * Add product fields fileset
     */
    private function _addProductFieldset() {
        $model = $this->getModel();
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();

        $fieldset = $form->addFieldset('product_fieldset', array(
            'legend' => $h->__('Product templates'),
            'class'  => 'fieldset-wide'
        ));

        foreach (range(1, 5) as $i) {
            $fieldset->addField("template_pr_txt_$i", 'textarea', array(
                'required' => false,
                'name'     => "template_pr_txt_$i",
                'label'    => $h->__("Product template nr %d (text)", $i),
                'title'    => $h->__("Template nr %d for product (text/plain)", $i),
                'value'    => $model->getId() !== null ? $model->getData("template_pr_txt_$i") : ''));
            $fieldset->addField("template_pr_html_$i", 'textarea', array(
                'required' => false,
                'name'     => "template_pr_html_$i",
                'label'    => $h->__("Product template nr %d (html)", $i),
                'title'    => $h->__("Template nr %d for product (html)", $i),
                'value'    => $model->getId() !== null ? $model->getData("template_pr_html_$i") : ''));
        }

        $fieldset->addField('default_product_snippet', 'text', array(
            'required' => false,
            'name'     => 'default_product_snippet',
            'label'    => $h->__('Default product snippet (1-5)'),
            'title'    => $h->__('Default product snippet number'),
            'value'    => $model->getId() !== null ? $model->getDefaultProductSnippet() : ''));

        $fieldset->addField('product_image_size', 'text', array(
            'required' => false,
            'name'     => 'product_image_size',
            'label'    => $h->__('Product image size (width x height)'),
            'title'    => $h->__('Product image size (width x height)'),
            'value'    => $model->getId() !== null ? $model->getProductImageSize() : ''));
    }


    /**
     * Add cron fields fileset
     */
    private function _addCronFieldset() {
        $model = $this->getModel();
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();
        $yesNo = Mage::getModel('adminhtml/system_config_source_yesno');

        $form = $this->getForm();
        $fieldset = $form->addFieldset('cron_fieldset', array(
            'legend' => $h->__('Cron Information'),
            'class'  => 'fieldset-narrow'
        ));

        $fieldset->addField('is_cron_enabled', 'select', array(
            'label'      => $h->__('Activate for cron execution'),
            'title'      => $h->__('Does the template is active for cron?'),
            'name'       => 'is_cron_enabled',
            'values'   => $yesNo->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getIsCronEnabled() : 0));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('cron_date_range_start', 'date', array(
            'label'        => $h->__('Enabled start date'),
            'title'        => $h->__('Start date for cron'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
            'required'     => false,
            'name'         => 'cron_date_range_start',
            'value'        => $model->getId() !== null ? $model->getCronDateRangeStart() : ''));
        $fieldset->addField('cron_date_range_end', 'date', array(
            'label'        => $h->__('Enabled end date'),
            'title'        => $h->__('End date for cron'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
            'required'     => false,
            'name'         => 'cron_date_range_end',
            'value'        => $model->getId() !== null ? $model->getCronDateRangeEnd() : ''));
        $fieldset->addField('queue_delay_time', 'text', array(
            'label'    => $h->__('Queue delay time'),
            'title'    => $h->__('Optional queue delay time'),
            'required' => false,
            'name'     => 'queue_delay_time',
            'value'    => $model->getId() !== null ? $model->getQueueDelayTime() : 0));

        $fieldset->addField('priority', 'text', array(
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
        $model = $this->getModel();
        /* @var $h Contactlab_Template_Helper_Data */
        $h = Mage::helper('contactlab_template');
        $form = $this->getForm();
        $andOr = Mage::getModel('contactlab_template/system_config_source_andOr');

        $fieldset = $form->addFieldset('customer_filter_fieldset', array(
            'legend' => $h->__('Customer filter options'),
            'class'  => 'fieldset-narrow'
        ));

        $fieldset->addField('min_minutes_from_last_update', 'text', array(
            'required' => false,
            'class' => 'validate-not-negative-number validate-digits validate-is-min-of-range',
            'label' => $h->__('Minimum number of minutes'),
            'title' => $h->__('Minimum number of minutes'),
            'name' => 'min_minutes_from_last_update',
            'value'    => $model->getId() !== null ? $model->getMinMinutesFromLastUpdate() : ''));

        $fieldset->addField('max_minutes_from_last_update', 'text', array(
            'required' => false,
            'class' => 'validate-not-negative-number validate-digits validate-is-max-of-range',
            'label' => $h->__('Maximum number of minutes'),
            'title' => $h->__('Maximum number of minutes'),
            'name' => 'max_minutes_from_last_update',
            'value'    => $model->getId() !== null ? $model->getMaxMinutesFromLastUpdate() : ''));

        $fieldset->addField('min_value', 'text', array(
            'required' => false,
            'class' => 'validate-number validate-not-negative-number validate-is-min-of-range',
            'label' => $h->__('Minimum value'),
            'title' => $h->__('Minimum value'),
            'comment' => '[EUR]',
            'name' => 'min_value',
            'value'    => $model->getId() !== null ? $h->formatPrice($model->getMinValue()) : ''));

        $fieldset->addField('max_value', 'text', array(
            'required' => false,
            'class' => 'validate-number validate-not-negative-number validate-is-max-of-range',
            'label' => $h->__('Maximum value'),
            'title' => $h->__('Maximum value'),
            'name' => 'max_value',
            'value'    => $model->getId() !== null ? $h->formatPrice($model->getMaxValue()) : ''));

        $fieldset->addField('min_products', 'text', array(
            'required' => false,
            'class' => 'validate-greater-than-zero validate-digits validate-is-min-of-range',
            'label' => $h->__('Minimum number of products'),
            'title' => $h->__('Minimum number of products'),
            'name' => 'min_products',
            'value'    => $model->getId() !== null ? $model->getMinProducts() : ''));

        $fieldset->addField('max_products', 'text', array(
            'required' => false,
            'class' => 'validate-greater-than-zero validate-digits validate-is-max-of-range',
            'label' => $h->__('Maximum number of products'),
            'title' => $h->__('Maximum number of products'),
            'name' => 'max_products',
            'value'    => $model->getId() !== null ? $model->getMaxProducts() : ''));

        $fieldset->addField('and_or', 'select', array(
            'required' => true,
            'label' => $h->__('And / Or condition values'),
            'title' => $h->__('And / Or condition values'),
            'name' => 'and_or',
            'values'   => $andOr->toOptionArray(),
            'value'    => $model->getId() !== null ? $model->getAndOr() : 'AND'));

        return $this;
    }
}
