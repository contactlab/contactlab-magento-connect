<?php


/** Newsletter XmlDelivery Model. */
class Contactlab_Template_Model_Newsletter_XmlDelivery extends Mage_Core_Model_Abstract {
    /**
     * Send.
     *
     * @return void
     */
    public function send() {
        $this->getUploader()->setTask($this->getTask())->setStoreId($this->getStoreId());
        $this->setSendId(rand());

        // Won't use Mage::getModel here.
        $this->setXml(new Contactlab_Template_Model_SimpleXMLExtended("<contactlab></contactlab>"));
        $this->_addAuth();
        $this->_addCampaign();

        $this->setXmlFileName(sprintf('xml-delivery-%010d.xml', $this->getSendId()));
        $xmlFilePath = $this->getUploader()->getOutputPath() . "/" . $this->getXmlFileName();
        $this->getTask()->addEvent("Saving xml file to $xmlFilePath");
        $semaphoreFilePath = "$xmlFilePath.ok";

        $this->getXml()->asXML($xmlFilePath);
        touch($semaphoreFilePath);

        if (!$this->getUploader()->useLocalServer()) {
            // Put sftp
            $this->getUploader()->addFileToUpload("XML Delivery", $xmlFilePath);
            $this->getUploader()->addFileToUpload("Semaphore", $semaphoreFilePath, false);
            $this->getUploader()->uploadAndRemoveLocal();
            try {
                $this->_createCheckTask();
                $this->getTask()->setPreventClose(true);
            } catch (Exception $e) {}
        }
        return $this->rv;
    }

    /**
     * Add auth xml node.
     *
     * @return SimpleXMLElement
     */
    private function _addAuth() {
        $auth = $this->getXml()->addChild('auth');
        $auth->addChild('suid', $this->_getAuthSuid());
        $auth->addChild('key', $this->_getAuthKey());
        return $auth;
    }

    /**
     * Add campaign xml node.
     *
     * @return SimpleXMLElement
     */
    private function _addCampaign() {
        $campaign = $this->getXml()->addChild('campaign');
        $media = $campaign->addChild($this->_getMedia());
        $media->addChild('newsletter', $this->_getNewsletter());

        $media->addChild('recipients')
                ->addChild('new_recipients')
                ->addChild('csv_file', $this->_getCsvFile());
        $media->addChild('delivery')->addAttribute('method', $this->_getDeliveryMethod());

        // TODO
        //$this->_addNotes($media->addChild('notes'));

        $message = $media->addChild('message');
        $this->_addMessage($message);

        return $campaign;
    }

    /**
     * Add notes.
     *
     * @param SimpleXMLElement $notes
     * @return void
     */
    private function _addNotes(SimpleXMLElement $notes) {
        // TODO
    }

    /**
     * Add message.
     *
     * @param SimpleXMLElement $message
     * @return void
     */
    private function _addMessage(SimpleXMLElement $message) {
        $message->addChild('encoding', $this->_getEncoding());
        $headers = $message->addChild('headers');
        $headers->addChild('subject', $this->_getSubject());
        $mailFrom = $headers->addChild('mail_from');
        $mailFrom->addChild('name', $this->_getMailFromName());
        $mailFrom->addChild('address', $this->_getMailFromEmail());

        if ($this->getTemplate()->hasReplyTo()) {
            $headers->addChild('reply_to', $this->getTemplate()->getReplyTo());
        }

        $message->addChild('publish_on_web')
            ->addAttribute('ovveridetaf', 'true');
        $message->addChild('preferred_content', $this->_getPreferredContent());

        $body = $message->addChild('body');
        $this->_addBody($body);
    }

    /**
     * Add body.
     *
     * @param SimpleXMLElement $body
     * @return void
     */
    private function _addBody(SimpleXMLElement $body) {
        if ($this->getTemplate()->getFlgHtmlTxt() !== 'T') {
            $el = $body->addChild('html');
            $el->addChild('content')
                ->addChild('embed')
                ->addCData('${html_template}$');
            $this->_addPreferences($el, true);
        }
        if ($this->getTemplate()->getFlgHtmlTxt() !== 'H') {
            $el = $body->addChild('text');
            $el->addChild('content')
                ->addChild('embed')
                ->addCData('${text_template}$');
            $this->_addPreferences($el, false);
        }
    }

    /**
     * Add preferences.
     *
     * @param SimpleXMLElement $to
     * @param unknown $prettyprint
     * @return void
     */
    private function _addPreferences(SimpleXMLElement $to, $prettyprint) {
        $stats = $to->addChild('preferences')->addChild('stats');
        $stats->addChild('links', 'parse');
        $stats->addChild('prettyprint')->addAttribute('enabled',
                $prettyprint ? 'true' : 'false');
        $stats->addChild('unescape')->addAttribute('enabled', 'false');
    }

    /**
     * Get auth suid.
     *
     * @return string
     */
    private function _getAuthSuid() {
        return $this->_getStoreConfig("auth_suid");
    }

    /**
     * Get auth key.
     *
     * @return string
     */
    private function _getAuthKey() {
        return $this->_getStoreConfig("auth_api_key");
    }

    /**
     * Get delivery method.
     *
     * @return string
     */
    private function _getDeliveryMethod() {
        return $this->_getStoreConfig("delivery_method");
    }

    /**
     * Get store config.
     *
     * @param string $key
     * @return string
     */
    private function _getStoreConfig($key, $prefix = "contactlab_template/queue") {
        return Mage::getStoreConfig("$prefix/$key",
            $this->getStoreId());
    }

    /**
     * Get media.
     *
     * @return string
     */
    private function _getMedia() {
        return "email";
    }

    /**
     * Get subject.
     *
     * @return string
     */
    private function _getSubject() {
        return $this->getTemplate()->getTemplateSubject();
    }

    /**
     * Get mail from name.
     *
     * @return string
     */
    private function _getMailFromName() {
        return $this->getTemplate()->getTemplateSenderName();
    }

    /**
     * Get mail from email.
     *
     * @return string
     */
    private function _getMailFromEmail() {
        return $this->getTemplate()->getTemplateSenderEmail();
    }

    /**
     * Get encoding.
     *
     * @return string
     */
    private function _getEncoding() {
        return "utf-8";
    }

    /**
     * Get preferred content.
     *
     * @return string
     */
    private function _getPreferredContent() {
        switch ($this->getTemplate()->getFlgHtmlTxt()) {
            case "B":
                return "both";
            case "T":
                return "simple_text";
            case "H":
                return "rich_text";
            default:
                return "";
        }
    }

    /**
     * Get newsletter.
     *
     * @return string
     */
    private function _getNewsletter() {
        return $this->_getStoreConfig("group_id");
    }

    /**
     * Get csv file.
     *
     * @return string, zipped csv file.
     */
    private function _getCsvFile() {
        $csvFileName = sprintf('recipients-%010d-list.csv', $this->getSendId());

        $csvFilePath = $this->getUploader()->getOutputPath() . "/$csvFileName";
        $zipFileName = "$csvFileName.zip";
        $zipFilePath = $this->getUploader()->getOutputPath() . "/$zipFileName";

        $fp = fopen($csvFilePath, 'w');

        fputcsv($fp, array('entity_id', 'email', 'full_name', 'html_template',
            'text_template', 'dob', 'gender', 'group', 'firstname', 'lastname',
            'middlename', 'prefix', 'suffix', 'store', 'website'));


        $store = Mage::getModel('core/store')->load($this->getStoreId());
        $counter = 0;

        /* @var $helper Contactlab_Commons_Helper_Data */
        $helper = Mage::helper("contactlab_commons");
        $helper->logWarn($this->getSourceCollection()->getSelect()->assemble());

        $this->getSourceCollection()->disableIdCheck();
        foreach ($this->getSourceCollection() as $item) {
            fputcsv($fp, array(
                $item->getUk(),
                $item->getEmail(),
                $this->_getFullName($item),
                $this->_getHtmlTemplate($item),
                $this->_getTextTemplate($item),
                $item->hasCustomerDob() ? $item->getCustomerDob() : '0000-00-00',
                $item->getCustomerGender(),
                $item->getCustomerGroup(),
                $item->getCustomerFirstname(),
                $item->getCustomerLastname(),
                $item->getCustomerMiddlename(),
                $item->getCustomerPrefix(),
                $item->getCustomerSuffix(),
                $store->getName(),
                $store->getWebsite()->getName()
            ));
            $counter++;
            if ($counter % 100 == 0) {
                $this->getTask()->setProgressValue($counter);
            }
        }
        $this->getSourceCollection()->enableIdCheck();
        $this->rv = sprintf("%d items added to XMLDelivery queue", $counter);
        $this->getTask()->setProgressValue($counter);

        $zip = new ZipArchive();
        $res = $zip->open($zipFilePath, ZipArchive::OVERWRITE);
        $zip->addFile($csvFilePath, $csvFileName);
        $zip->close();
        unlink($csvFilePath);

        if (!$this->getUploader()->useLocalServer()) {
            $this->getUploader()->addFileToUpload("Zipped CSV", $zipFilePath);
        }

        return $zipFileName;
    }

    /**
     * Get full name.
     *
     * @param Mage_Core_Model_Abstract $item
     * @return string
     */
    private function _getFullName(Mage_Core_Model_Abstract $item) {
        $rv = '';
        if ($item->getCustomerPrefix()) {
            $rv .= $item->getCustomerPrefix() . ' ';
        }
        $rv .= $item->getCustomerFirstname();
        if ($item->getCustomerMiddlename()) {
            $rv .= ' ' . $item->getCustomerMiddlename();
        }
        $rv .=  ' ' . $item->getCustomerLastname();
        if ($item->getCustomerSuffix()) {
            $rv .= ' ' . $item->getCustomerSuffix();
        }
        return $rv;
    }

    /**
     * Get html template.
     *
     * @param Mage_Core_Model_Abstract $item
     * @return string
     */
    private function _getHtmlTemplate(Mage_Core_Model_Abstract $item) {
        return $this->_getTemplate($item, 'html');
    }

    /**
     * Get text template.
     *
     * @param Mage_Core_Model_Abstract $item
     * @return string
     */
    private function _getTextTemplate(Mage_Core_Model_Abstract $item) {
        return $this->_getTemplate($item, 'text');
    }

    /**
     * Get template.
     *
     * @param Mage_Core_Model_Abstract $item
     * @param string $type
     * @return string
     */
    private function _getTemplate(Mage_Core_Model_Abstract $item, $type) {
        return Mage::getSingleton("contactlab_template/newsletter_template_compiler_$type")
            ->setStoreId($this->getStoreId())
            ->compile($this->getTemplate(), $item);
    }

    /**
     * Create check task.
     *
     * @return Contactlab_Commons_Model_Task
     */
    private function _createCheckTask() {
        return Mage::getModel("contactlab_commons/task")
                ->setDescription(sprintf('Check %s queue', $this->getTask()->getDescription()))
                ->setTaskCode("CheckNewsletterQueueTask")
                ->setModelName('contactlab_template/task_checkNewsletterQueueRunner')
                ->setTaskData(serialize(array(
                    'task_id' => $this->getTask()->getTaskId(),
                    'xml_filename' => $this->getXmlFileName(),
                    'store_id' => $this->getStoreId(),
                    'queue_id' => $this->getQueueId()
                )))
                ->save();
    }

    /**
     * Get uploader.
     *
     * @return Contactlab_Template_Model_Newsletter_XmlDelivery_Uploader
     */
    public function getUploader() {
        // Init uploader
        if (!$this->hasUploader()) {
            $this->setUploader(Mage::getModel('contactlab_template/newsletter_xmlDelivery_uploader'));
        }
        return parent::getUploader();
    }
}

