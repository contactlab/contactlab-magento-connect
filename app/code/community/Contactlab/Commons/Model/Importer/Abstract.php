<?php

/** Abstract Importer Model. */
abstract class Contactlab_Commons_Model_Importer_Abstract extends Contactlab_Commons_Model_AbstractImportExport {
    /** Import the xml file. */
    public final function import(Contactlab_Commons_Model_Task_Interface $task) {
        if (!$this->isEnabled()) {
            Mage::helper("contactlab_commons")->logWarn("Module import is disabled");
            return "Module import is disabled";
        }

        if ($this->_useLocalServer()) {
            $filename = $this->getTask()->getConfig("contactlab_commons/connection/import_local_path")
                    . '/' . $this->getFileName();
			$filename = $this->_formatFileName($filename);
	        Mage::helper("contactlab_commons")->logNotice("Importing locally from $filename");

			if (file_exists($filename)) {
				// Hmm, sure to load xml in memory?
				$this->importXml(simplexml_load_file("compress.zlib://$filename"));
			} else {
				throw new Zend_Exception("Could not find local $filename");
			}
        } else if ($this->_useRemoteServer()) {
            $filename = $this->_formatFileName($this->getFileName());
			$localFile = $this->_getFile($filename);
			$this->importXml(simplexml_load_file("compress.zlib://$localFile"));
			if (!Mage::helper('contactlab_commons')->isDebug()) {
				unlink($localFile);
			}
		}
        return "Import done";
    }

    /** Get file from sftp into localhost. */
    private function _getFile($filename) {
        if ($this->_useRemoteServer()) {
            $sftp = new Contactlab_Commons_Model_Ssh_Net_SFTP(
                    $this->getTask()->getConfig("contactlab_commons/connection/remote_server"));
            if (!$sftp->login(
                    $this->getTask()->getConfig("contactlab_commons/connection/sftp_username"),
                    $this->getTask()->getConfig("contactlab_commons/connection/sftp_password"))) {
                throw new Zend_Exception('Login Failed');
            }
			$remoteFile = $this->getTask()->getConfig("contactlab_commons/connection/import_remote_path")
	                . '/' . basename($filename);
			$localFile = "/tmp/$filename";
			$localDirName = dirname($localFile);
			if (!is_dir($localDirName)) {
            	throw new Zend_Exception("$localDirName is not a valid path");
			}
			if (!is_dir_writeable($localDirName)) {
            	throw new Zend_Exception("$localDirName is not writeable");
			}
			if (file_exists($localFile) && !is_writable($localFile)) {
            	throw new Zend_Exception("$localFile is not writeable");
			}
			Mage::helper("contactlab_commons")->logNotice("Remote file is $remoteFile");
            if ($sftp->get($remoteFile, $localFile)) {
				$this->getTask()->addEvent("File copied from $remoteFile to $localFile");
            } else {
            	throw new Zend_Exception("Failed copy from $remoteFile");
			}
			$sftp->_disconnect(0);

			return $localFile;
        }
    }

        /** Get xml object. */
    protected abstract function importXml(SimpleXMLElement $xml);
}
