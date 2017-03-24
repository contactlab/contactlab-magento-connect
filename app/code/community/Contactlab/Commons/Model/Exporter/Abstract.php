<?php

/**
 * Abstract Exporter Model.
 * @property resource gz
 *
 * @method Contactlab_Commons_Model_Task getTask
 */
abstract class Contactlab_Commons_Model_Exporter_Abstract extends Contactlab_Commons_Model_AbstractImportExport {
    /** Export the xml file.
     * @param Contactlab_Commons_Model_Task_Interface $task
     * @return string
     * @throws Zend_Exception
     */
    public function export(Contactlab_Commons_Model_Task_Interface $task) {
        if (!$this->isEnabled()) {
            Mage::helper("contactlab_commons")->logWarn("Module export is disabled");
            return "Module export is disabled";
        }
        if ($this->_useLocalServer()) {
            $filenameFormat = $this->getTask()->getConfig("contactlab_commons/connection/export_local_path")
                    . '/' . $this->getFileName();
        } else {
            $filenameFormat = "/tmp/" . $this->getFileName();
        }
        if ($this->_useRemoteServer()) {
            $filename = $this->_formatFileName($filenameFormat, true);
            $realFilename = $this->_formatFileName($filenameFormat);
        } else {
            $filename = $this->_formatFileName($filenameFormat);
        }
        Mage::helper("contactlab_commons")->logNotice("Exporting locally to $filename");

        $path = dirname($filename);

        if (is_dir($path) && !is_writable($path)) {
            Mage::helper("contactlab_commons")->logAlert("$path is not writeable");
            throw new Zend_Exception("$path is not writeable");
        }
        if (is_file($filename) && !is_writable($filename)) {
            Mage::helper("contactlab_commons")->logAlert("$filename is not writeable");
            throw new Zend_Exception("$filename is not writeable");
        }
        if (($this->gz = gzopen($filename, 'w9')) === false) {
            Mage::helper("contactlab_commons")->logAlert("Could not export to $filename");
            throw new Zend_Exception("Could not export to $filename");
        }

		gzwrite($this->gz, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<dataroot>\n");
		$this->writeXml();
		gzwrite($this->gz, "</dataroot>\n");
        gzclose($this->gz);

		if ($this->_useRemoteServer()) {
        	$this->_putFile(realpath($filename), basename($realFilename));
            sleep(2);
            unlink(realpath($filename));
        }
        $this->afterFileCopy();

        return "Export done";
    }

    /** Put file into sftp or localhost. */
    private function _putFile($filename, $realFilename) {
        $sftp = new Contactlab_Commons_Model_Ssh_Net_SFTP(
                $this->getTask()->getConfig("contactlab_commons/connection/remote_server"));
        if (!$sftp->login(
                $this->getTask()->getConfig("contactlab_commons/connection/sftp_username"),
                $this->getTask()->getConfig("contactlab_commons/connection/sftp_password"))) {
            throw new Zend_Exception('Login Failed');
        }
        $remoteFile = $this->getTask()
                ->getConfig("contactlab_commons/connection/export_remote_path")
                . '/' . $realFilename;
        $sftp->delete($remoteFile);
        $sftp->put($remoteFile, $filename, NET_SFTP_LOCAL_FILE);
        $this->_checkUploadedFile($filename, $remoteFile, $sftp);
        
		$sftp->_disconnect(0);
    }

    /** Check uploaded file existence. */
    private function _checkUploadedFile($localFile, $remoteFile, $sftp) {
        $localFileSize = filesize($localFile);
        $remoteStat = $sftp->lstat($remoteFile);
        if (!$remoteStat) {
            throw new Zend_Exception(sprintf('There\'s been a problem during file upload: uploaded file %s not found', $remoteFile));
        }
        $this->getTask()->addEvent("Remote file info: " . print_r($remoteStat, true));
        $remoteFileSize = $remoteStat['size'];
        if ($localFileSize != $remoteFileSize) {
            throw new Zend_Exception(sprintf(
                'There\'s been a problem during file upload: original (%s) file\'s lenght is %d while uploaded '
                    . '(%s) file\'s lenght is %d!', $localFile, $localFileSize, $remoteFile, $remoteFileSize));
        }
    }

    /** Write xml object. */
    protected abstract function writeXml();

    /**
     * Called after the export.
     */
    public function afterExport() {
    }

    /**
     * Called after the export.
     */
    public function afterFileCopy() {
    }
}
