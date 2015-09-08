<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml Newsletter Template Edit Form Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Contactlab_Template_Block_Adminhtml_Type_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Define Form settings
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieve template object
     *
     * @return Mage_Newsletter_Model_Template
     */
    public function getModel()
    {
        return Mage::registry('_current_template_type');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
     */
    protected function _prepareForm()
    {
        $model  = $this->getModel();

        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('contactlab_template')->__('Template Type Information'),
            'class'     => 'fieldset-wide'
        ));

        $fieldsetDnd = $form->addFieldset('dnd_fieldset', array(
            'legend'    => Mage::helper('contactlab_template')->__('Do not disturb values'),
            'class'     => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $model->getId(),
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('contactlab_template')->__('Template type name'),
            'title'     => Mage::helper('contactlab_template')->__('Template type name'),
            'required'  => true,
            'value'     => $model->getName(),
        ));
        $toDisable = array();
        $toDisable[] = $fieldset->addField('template_type_code', 'text', array(
            'name'      => 'template_type_code',
            'label'     => Mage::helper('contactlab_template')->__('Code of template type'),
            'title'     => Mage::helper('contactlab_template')->__('Code of template type'),
            'required'  => true,
            'value'     => $model->getTemplateTypeCode(),
        ));
        $fieldset->addField('is_system', 'select', array(
            'name'      => 'is_system',
            'label'     => Mage::helper('contactlab_template')->__('Is a system defined type'),
            'title'     => Mage::helper('contactlab_template')->__('Is a system defined type'),
            'value'     => $model->getIsSystem(),
            'readonly'  => true,
            'disabled'  => true,
            'options'   => array(
                1 => Mage::helper('adminhtml')->__('System'),
                0 => Mage::helper('adminhtml')->__('Custom')
            ),
        ));
        $toDisable[] = $fieldset->addField('is_cron_enabled', 'select', array(
            'name'      => 'is_cron_enabled',
            'label'     => Mage::helper('contactlab_template')->__('Enable for Cron execution'),
            'title'     => Mage::helper('contactlab_template')->__('Enable for Cron execution'),
            'after_element_html'   => "<small>" . Mage::helper('contactlab_template')->__('Default for new templates') . "</small>",
            'options'   => array(
                1 => Mage::helper('adminhtml')->__('Yes'),
                0 => Mage::helper('adminhtml')->__('No')
            ),
            'value'     => $model->getIsCronEnabled(),
        ));
        if ($model->getIsSystem()) {
            foreach ($toDisable as $field) {
                $field->setDisabled(true);
                $field->setReadonly(true);
            }
        }

        $fieldsetDnd->addField('dnd_period', 'text', array(
            'name'      => 'dnd_period',
            'class' => 'validate-not-negative-number validate-digits',
            'label'     => Mage::helper('contactlab_template')->__('DND period length'),
            'title'     => Mage::helper('contactlab_template')->__('DND period length'),
            'required'  => false,
            'value'     => $model->getDndPeriod(),
        ));
        $fieldsetDnd->addField('dnd_mail_number', 'text', array(
            'name'      => 'dnd_mail_number',
            'class' => 'validate-not-negative-number validate-digits',
            'label'     => Mage::helper('contactlab_template')->__('DND max mail number'),
            'title'     => Mage::helper('contactlab_template')->__('DND max mail number'),
            'required'  => false,
            'value'     => $model->getDndMailNumber(),
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
