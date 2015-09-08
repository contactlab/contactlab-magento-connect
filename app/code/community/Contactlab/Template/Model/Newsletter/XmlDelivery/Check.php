<?php

/**
 * XMLDelivery report file check.
 * @method Contactlab_Template_Model_Newsletter_XmlDelivery_Check setTask(Contactlab_Commons_Model_Task $task)
 * @method Contactlab_Template_Model_Newsletter_XmlDelivery_Check setParentTask(Contactlab_Commons_Model_Task $task)
 * @method Contactlab_Template_Model_Newsletter_XmlDelivery_Check setXmlFile(String $xmlFile)
 * @method Contactlab_Template_Model_Newsletter_XmlDelivery_Check setStoreId(String $storeId)
 * @method Contactlab_Template_Model_Newsletter_XmlDelivery_Check setQueueId(int $queueId)
 *
 * @method Contactlab_Commons_Model_Task getTask()
 * @method Contactlab_Commons_Model_Task getParentTask()
 * @method String getXmlFile()
 * @method String getStoreId()
 * @method int getQueueId()
 */
class Contactlab_Template_Model_Newsletter_XmlDelivery_Check extends Varien_Object {
    /**
     * Do check.
     * @return string
     * @throws Exception
     */
    public function doCheck() {
        $rv = false;
        $sftp = new Contactlab_Commons_Model_Ssh_Net_SFTP(
                $this->_getConfig("contactlab_commons/connection/remote_server"));
        if (!$sftp->login(
                $this->_getConfig("contactlab_commons/connection/sftp_username"),
                $this->_getConfig("contactlab_commons/connection/sftp_password"))) {
            throw new Zend_Exception('Login Failed');
        }
        $path = $this->_getRelativePath();

        $files = $sftp->nlist($path);
        $found = false;
        $doFinishQueueAndLinks = true;
        try {
            foreach ($files as $file) {
                if (strrpos($file, 'report.' . $this->getXmlFile()) === false) {
                    continue;
                }
                $found = true;
                if (($rv = $this->doCheckFile($sftp, $file)) !== false) {
                    if (preg_match('|Userdb\s+is\s+locked\s+\d+|', $rv)) {
                        $this->getParentTask()->retryOrFail();
                        $doFinishQueueAndLinks = false;
                    } else {
                        $this->getParentTask()->addEvent($rv, true);
                        $this->getParentTask()->setFailed();
                        $doFinishQueueAndLinks = false;
                    }
                } else {
                    if (!$this->getParentTask()->isClosed()) {
                        $this->getParentTask()->setStatus(
                            Contactlab_Commons_Model_Task::STATUS_CLOSED)->save();
                    }
                }
            }
        } catch (Zend_Exception $e) {
    		$sftp->_disconnect(0);
    		throw $e;
        }

        if (!$found) {
            if ($this->getTask()->getNumberOfRetries() == $this->getTask()->getMaxRetries() - 1
                    && $this->getParentTask()->getTaskId()) {
                // Fail parent task
                $rv = "Report file not found!";
                $this->getParentTask()->addEvent($rv, true);
                $this->getParentTask()->setFailed();
            } else {
                $rv = sprintf("Report file not yet found (retry %d/%d)",
                        $this->getTask()->getNumberOfRetries() + 1,
                        $this->getTask()->getMaxRetries());
            }
            $this->getTask()->setSuppressNotification(true);
            throw new Zend_Exception($rv);
        }
        $this->getTask()->setAutoDelete(true);

        if ($doFinishQueueAndLinks) {
            $this->finishQueueAndLinks();
        }
        return $rv;
    }

    /**
     * Finish queue and links.
     *
     * @return void
     */
    public function finishQueueAndLinks() {
        /** @var $queue Contactlab_Template_Model_Newsletter_Queue */
        $queue = Mage::getModel('newsletter/queue')->load($this->getQueueId());
        if ($queue->hasQueueId()) {
            $queue->finishQueueAndLinks();
        }
    }

    /**
     * Do check file.
     *
     * @param Contactlab_Commons_Model_Ssh_Net_SFTP $sftp
     * @param string $file
     * @return string
     */
    public function doCheckFile(Contactlab_Commons_Model_Ssh_Net_SFTP $sftp, $file) {
        $options = Mage::getModel('core/config_options');
        $localFile = tempnam($options->getVarDir() . DS . 'report', "xmldelivery-report");

        $sftp->get($this->_getRelativePath() . '/' . $file, $localFile);

        $report = simplexml_load_file($localFile);
        unlink($localFile);

        if (trim((string) $report->error) != '') {
            return (string) $report->error;
        } else {
            return false;
        }
    }

    /**
     * Get config.
     *
     * @param string $path
     * @return String
     */
    private function _getConfig($path) {
        return Mage::getStoreConfig($path, $this->getStoreId());
    }

    /**
     * Get delivery method.
     *
     * @return string
     */
    private function _getRelativePath() {
        return $this->_getConfig("contactlab_template/queue/export_remote_path");
    }
}
