<?php

/** Template types controller. */
class Contactlab_Template_Adminhtml_TypesController extends Mage_Adminhtml_Controller_Action {

    /** Index action. */
    public function indexAction() {
        $this->_title($this->__('Template types'));
        $this->loadLayout()->_setActiveMenu('newsletter/contactlab');
        return $this->renderLayout();
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('newsletter/contactlab/template_types');
    }

    /**
     * Set title of page
     *
     * @return Mage_Adminhtml_Newsletter_TemplateController
     */
    protected function _setTitle()
    {
        return $this->_title($this->__('Newsletter'))->_title($this->__('Newsletter Templates Types'));
    }


    /**
     * Create new Template Type
     *
     */
    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * Edit Template Type
     *
     */
    public function editAction() {
        $this->_setTitle();

        $model = Mage::getModel('contactlab_template/type');
        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('_current_template_type', $model);

        $this->loadLayout();
        $this->_setActiveMenu('newsletter/contactlab');

        if ($model->getId()) {
            $breadcrumbTitle = Mage::helper('contactlab_template')->__('Edit Template Type');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = Mage::helper('contactlab_template')->__('New Template Type');
            $breadcrumbLabel = Mage::helper('contactlab_template')->__('Create Template Type');
        }

        $this->_title($model->getId() ? $model->getTemplateCode() : $this->__('New Template Type'));

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        if ($values = $this->_getSession()->getData('contactlab_template_type_form_data', true)) {
            $model->addData($values);
        }

        if ($editBlock = $this->getLayout()->getBlock('contactlab_template_edit')) {
            $editBlock->setEditMode($model->getId() > 0);
        }

        $this->renderLayout();
    }

    /**
     * Save Newsletter Template
     *
     */
    public function saveAction() {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $model = Mage::getModel('contactlab_template/type');

        if ($id = (int)$request->getParam('id')) {
            $model->load($id);
        }

        try {
            $model->addData($request->getParams())
                ->setName($request->getParam('name'))
                ->setTemplateTypeCode($request->getParam('template_type_code'))
                ->setIsSystem($request->getParam('is_system'))
                ->setIsCronEnabled($request->getParam('is_cron_enabled'));

            $model->save();
            $this->_redirect('*/*');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('contactlab_template_type_form_data',
                $this->getRequest()->getParams());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('contactlab_template')->__('An error occurred while saving this template type.'));
            $this->_getSession()->setData('contactlab_template_type_form_data', $this->getRequest()->getParams());
        }
        $this->_forward('new');
    }

    /**
     * Delete newsletter Template
     *
     */
    public function deleteAction() {
        $template = Mage::getModel('contactlab_template/type')
            ->load($this->getRequest()->getParam('id'));
        if ($template->getIsSystem()) {
            $this->_getSession()->addException(new Zend_Exception(Mage::helper('contactlab_template')->__('Can\'t delete a system defined template!')),
                    Mage::helper('contactlab_template')->__('Can\'t delete a system defined template!'));
            $this->_redirect('*/*');
            return;
        }
        if ($template->getId()) {
            try {
                $template->delete();
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('contactlab_template')->__('An error occurred while deleting this template type.'));
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Grid action.
     */
    public function gridAction() {
        return $this->loadLayout(false)->renderLayout();
    }
}
