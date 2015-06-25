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
	 * @return string
	 */
	protected function _formatFileName($filename) {
		if (!preg_match("|^.*\.gz$|", $filename)) {
			$filename = $filename . '.gz';
		}
		$format = preg_replace('|.*{([^}]+)}.*|', '\1', $filename);
		if (!$format) {
			return $filename;
		}
		$formatted = date($format);
		return preg_replace("|\{$format\}|", "$formatted", $filename);
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
}
