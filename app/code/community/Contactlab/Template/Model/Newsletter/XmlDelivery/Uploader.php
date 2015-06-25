<?php


/**
 * XMLDelivery uploader.
 */
class Contactlab_Template_Model_Newsletter_XmlDelivery_Uploader extends Varien_Object {
    /**
     * Set store id.
     *
     * @param string $storeId
     * @return $this
     */
    public function setStoreId($storeId) {
        $rv = parent::setStoreId($storeId);
        $this->_init();
        return $rv;
    }

    /**
     * Init.
     *
     * @return void
     */
    private function _init() {
        if ($this->useLocalServer()) {
            $this->setOutputPath($this
                ->getConfig("contactlab_commons/connection/export_local_path")
                    . "/" . $this->_getRelativePath());
            if (!is_dir($this->getOutputPath())) {
                mkdir($this->getOutputPath(), 0755, true);
            }
        } else {
            $this->setOutputPath("/tmp");
        }
        $this->setToUploadFiles(new Varien_Data_Collection());
    }

    /**
     * Copy to local server?
     *
     * @return boolean
     */
    public function useLocalServer() {
        return $this->getConfig("contactlab_commons/connection/type") == 1;
    }

    /**
     * Get delivery method.
     *
     * @return string
     */
    private function _getRelativePath() {
        return $this->getConfig("contactlab_template/queue/export_remote_path");
    }

    public function addFileToUpload($name, $path, $doCheck = true) {
        $item = new Varien_Object();
        $item->setName($name);
        $item->setPath($path);
        $item->setId($path);
        $item->setDoCheck($doCheck);
        $this->getToUploadFiles()->addItem($item);
    }

    /**
     * Upload and remove local files.
     *
     * @return void
     */
    public function uploadAndRemoveLocal() {
        $this->upload();
        $this->removeLocal();
    }

    /**
     * Upload files.
     *
     * @return void
     */
    public function upload() {
        foreach ($this->getToUploadFiles() as $item) {
            Mage::helper('contactlab_commons')->logInfo(sprintf("Upload %s (%s)",
                $item->getName(), $item->getPath()));
            $this->_putFile($item->getPath(), $item->getDoCheck());
        }
    }

    /**
     * Remove local files.
     *
     * @return void
     */
    public function removeLocal() {
        foreach ($this->getToUploadFiles() as $item) {
            Mage::helper('contactlab_commons')->logInfo(sprintf("Remove %s (%s)",
                $item->getName(), $item->getPath()));
            unlink($item->getPath());
        }
        $this->getToUploadFiles()->clear();
    }

    /**
     * Put file to sftp.
     *
     * @param string $filename
     * @return void
     */
    private function _putFile($filename, $doCheck) {
        $sftp = $this->getSFTPConnection();
        $remoteFile = $this->_getRelativePath() . '/' . basename($filename);
        $sftp->delete($remoteFile);
        $sftp->put($remoteFile, $filename, NET_SFTP_LOCAL_FILE);
        if ($doCheck) {
            $this->_checkUploadedFile($filename, $remoteFile, $sftp);
        }
	$sftp->_disconnect(0);
    }

    /**
     * Get SFTP connection.
     * @return Contactlab_Commons_Model_Ssh_Net_SFTP
     * @throws Zend_Exception
     */
    public function getSFTPConnection() {
        $sftp = new Contactlab_Commons_Model_Ssh_Net_SFTP(
                $this->getConfig("contactlab_commons/connection/remote_server"));
        if (!$sftp->login(
                $this->getConfig("contactlab_commons/connection/sftp_username"),
                $this->getConfig("contactlab_commons/connection/sftp_password"))) {
            throw new Zend_Exception('Login Failed');
        }
        return $sftp;
    }

    /**
     * Check uploaded file existence.
     *
     * @param string $localFile
     * @param string $remoteFile
     * @param Contactlab_Commons_Model_Ssh_Net_SFTP $sftp
     * @return void
     */
    private function _checkUploadedFile($localFile, $remoteFile, Contactlab_Commons_Model_Ssh_Net_SFTP $sftp) {
        $localFileSize = filesize($localFile);
        $remoteStat = $sftp->lstat($remoteFile);
        if (!$remoteStat) {
            throw new Zend_Exception(sprintf('There\'s been a problem during file upload: uploaded file %s not found', $remoteFile));
        }
        if ($this->hasTask()) {
            $this->getTask()->addEvent("Remote file info: " . print_r($remoteStat, true));
        }

        $remoteFileSize = $remoteStat['size'];
        if ($localFileSize != $remoteFileSize) {
            throw new Zend_Exception(sprintf(
                'There\'s been a problem during file upload: original (%s) file\'s lenght is %d while uploaded '
                    . '(%s) file\'s lenght is %d!', $localFile, $localFileSize, $remoteFile, $remoteFileSize));
        }
    }

    /**
     * Get config.
     *
     * @param string $path
     * @return void
     */
    private function getConfig($path) {
        return Mage::getStoreConfig($path, $this->getStoreId());
    }
}
