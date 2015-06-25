<?php

/** Contactlab template block adminhtml type edit. */
class Contactlab_Template_Block_Adminhtml_Type_Edit extends Mage_Adminhtml_Block_Widget
{
    /**
     * Edit Block model
     *
     * @var bool
     */
    protected $_editMode = false;

    /**
     * Retrieve template object
     *
     * @return Mage_Newsletter_Model_Template
     */
    public function getModel() {
        return Mage::registry('_current_template_type');
    }

    /**
     * Preparing block layout
     *
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit
     */
    protected function _prepareLayout() {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Back'),
                    'onclick'   => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class'     => 'back'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('contactlab_template')->__('Reset'),
                    'onclick'   => 'window.location.href = window.location.href'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('contactlab_template')->__('Save Template Type'),
                    'onclick'   => 'templateControl.save();',
                    'class'     => 'save'
                ))
        );

        if (!$this->getModel()->getIsSystem()) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('contactlab_template')->__('Delete Template Type'),
                        'onclick'   => 'templateControl.deleteTemplate();',
                        'class'     => 'delete'
                    ))
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml() {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml() {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml() {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml() {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Set edit flag for block
     *
     * @param boolean $value
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit
     */
    public function setEditMode($value = true) {
        $this->_editMode = (bool)$value;
        return $this;
    }

    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode() {
        return $this->_editMode;
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText() {
        if ($this->getEditMode()) {
            return Mage::helper('contactlab_template')->__('Edit Template Type');
        }

        return  Mage::helper('contactlab_template')->__('New Template Type');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm() {
        return $this->getLayout()
            ->createBlock('contactlab_template/adminhtml_type_edit_form')
            ->toHtml();
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl() {
        return $this->getUrl('*/*/save');
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl() {
        return $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id')));
    }

    /**
     * Getter for single store mode check
     *
     * @return boolean
     */
    protected function isSingleStoreMode() {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * Getter for id of current store (the only one in single-store mode and current in multi-stores mode)
     *
     * @return boolean
     */
    protected function getStoreId() {
        return Mage::app()->getStore(true)->getId();
    }
}
