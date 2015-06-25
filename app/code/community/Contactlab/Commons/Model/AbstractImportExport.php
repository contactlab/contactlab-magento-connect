<?php

/** Abstract Exporter Model. */
abstract class Contactlab_Commons_Model_AbstractImportExport extends Mage_Core_Model_Abstract {
    /**
     * Copy to remote server?
     *
     * @return boolean
     */
    protected function _useRemoteServer() {
        return $this->getTask()->getConfig("contactlab_commons/connection/type") == 0;
    }

    /**
     * Copy to local server?
     *
     * @return boolean
     */
    protected function _useLocalServer() {
        return $this->getTask()->getConfig("contactlab_commons/connection/type") == 1;
    }

    /**
	 * Format file name.
	 *
	 * @param string $filename
     * @param boolean $addTime
	 * @return string
	 */
	protected function _formatFileName($filename, $addTime = false) {
		if (!preg_match("|^.*\.gz$|", $filename)) {
			$filename = $filename . '.gz';
		}
		$format = preg_replace('|.*{([^}]+)}.*|', '\1', $filename);
		if (!$format) {
			return $filename;
		}
		$formatted = date($format);
        $rv = preg_replace("|\{$format\}|", "$formatted", $filename);
        if ($addTime) {
            $path = dirname($rv);
            $filenameWithTime = $this->_getTime() . '_' . basename($rv);
            $rv = $path . '/' . $filenameWithTime;
        }
		return $rv;
	}

    /**
     * Get file name.
     *
     * @return string
     */
    protected abstract function getFileName();

    /**
     * Is export enabled.
     *
     * @return boolean
     */
    protected abstract function isEnabled();
    
    /**
     * Get time string.
     * @return string.
     */
    private function _getTime() {
        $date = new DateTime();
        return $date->format('His');
    }
}
