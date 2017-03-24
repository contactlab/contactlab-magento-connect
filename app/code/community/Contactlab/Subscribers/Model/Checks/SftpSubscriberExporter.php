<?php

/**
 * Created by PhpStorm.
 * User: andreag
 * Date: 06/10/15
 * Time: 9.30
 */
class Contactlab_Subscribers_Model_Checks_SftpSubscriberExporter extends Contactlab_Subscribers_Model_Checks_AbstractCheck
{

    /**
     * Start check.
     * @return String
     */
    protected function doCheck()
    {
        return $this->_getCheckSFTP()
            ? $this->success(sprintf("SFTP test ok"))
            : $this->error(sprintf("SFTP test error"));
    }

    /**
     * Get code.
     * @return String
     */
    public function getCode()
    {
        return "sftp";
    }

    /**
     * Get description.
     * @return String
     */
    public function getDescription()
    {
        return "SFTP Subscriber Exporter test";
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 190;
    }

    /**
     * Check wsdl.
     */
    private function _getCheckSFTP()
    {
    	$path = Mage::getBaseDir('var').'/contactlab/export/';    		
		if(!is_dir($path)) 
		{
			mkdir($path, 0755, true);
		}
    	$filename = $path.'CheckSFTP.txt';    	
    	$fh = fopen($filename,'w');    	
		fwrite($fh, $this->getCode(),1024);
    	fclose($fh);    	    		
        $return = $this->_putFile($filename);
        unlink($filename);        	
    	return $return;
    }

     /**
     * Put file to sftp.
     *
     * @param string $filename
     * @return void
     */
    private function _putFile($filename) 
    {
        if($sftp = $this->getSFTPConnection())
        {
	        $remoteFile = $this->_getRelativePath() . '/' . basename($filename);	        
	        $sftp->delete($remoteFile);
	        $sftp->put($remoteFile, $filename, NET_SFTP_LOCAL_FILE);
	        $return = $this->_checkUploadedFile($filename, $remoteFile, $sftp);
	        $sftp->delete($remoteFile);
			$sftp->_disconnect(0);
        }
        else
        {
        	$return = false;
        }
        return $return;
    }

    /**
     * Get delivery method.
     *
     * @return string
     */
    private function _getRelativePath() {
    	return Mage::getStoreConfig("contactlab_commons/connection/export_remote_path");
    }
    
    /**
     * Get SFTP connection.
     * @return Contactlab_Commons_Model_Ssh_Net_SFTP
     * @throws Zend_Exception
     */
    public function getSFTPConnection() 
    {
        $sftp = new Contactlab_Commons_Model_Ssh_Net_SFTP(
                Mage::getStoreConfig("contactlab_commons/connection/remote_server")
        	);
        if (!$sftp->login(
                	Mage::getStoreConfig("contactlab_commons/connection/sftp_username"),
                	Mage::getStoreConfig("contactlab_commons/connection/sftp_password")
        		)
        	) 
        {
            return false;
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
    private function _checkUploadedFile($localFile, $remoteFile, Contactlab_Commons_Model_Ssh_Net_SFTP $sftp) 
    {
        $localFileSize = filesize($localFile);
        $remoteStat = $sftp->lstat($remoteFile);
        $return = true;
        if (!$remoteStat) {
            $return = false;
        }
        if ($this->hasTask()) {
            $this->getTask()->addEvent("Remote file info: " . print_r($remoteStat, true));
        }

        $remoteFileSize = $remoteStat['size'];
        if ($localFileSize != $remoteFileSize) {
            $return = false;
        }
        return $return;
    }
    
    

}