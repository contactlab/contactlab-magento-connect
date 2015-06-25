<?php

require_once('Mage/Adminhtml/controllers/Newsletter/TemplateController.php');

/**
 * Manage Newsletter Template Controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Contactlab_Template_Adminhtml_Newsletter_TemplateController
        extends Mage_Adminhtml_Newsletter_TemplateController {


    /**
     * Filtering posted data. Converting localized data if needed.
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('cron_date_range_start', 'cron_date_range_end'));
        return $data;
    }

    /**
     * Save Newsletter Template.
     */
    public function saveAction() {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/newsletter_template'));
        }
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            $request->setParam('cron_date_range_start', $data['cron_date_range_start']);
            $request->setParam('cron_date_range_end', $data['cron_date_range_end']);
        }
        $template = Mage::getModel('newsletter/template');

        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $template->addData($request->getParams())
                ->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setTemplateStyles($request->getParam('styles'))
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());
            if (!$template->getId()) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_HTML);
            }
            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }
            if ($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }
            $template->save();
            $this->_redirect('*/*');
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('newsletter_template_form_data',
                $this->getRequest()->getParams());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('contactlab_template')
                    ->__('An error occurred while saving this template.'));
            $this->_getSession()->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        }
        $this->_forward('new');
    }
}
