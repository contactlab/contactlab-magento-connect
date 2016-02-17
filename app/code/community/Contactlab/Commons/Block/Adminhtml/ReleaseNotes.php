<?php

/**
 * Rel notes block.
 */
class Contactlab_Commons_Block_Adminhtml_ReleaseNotes extends Mage_Adminhtml_Block_Widget_Container {
    /**
     * Construct
     */
     protected function _construct()
     {
         $this->_headerText = "ContactLab Release Notes";
         parent::_construct();
     }
     
     /**
      * Gets release notes collection.
      * @return \Varien_Data_Collection
      */
     public function getReleaseNotes() {
        $rv = new Varien_Data_Collection();
        /* @var $helper Contactlab_Commons_Helper_Data */
        $helper = Mage::helper('contactlab_commons');
        foreach ($helper->getModulesVersion() as $module) {
            $notes = $this->_findReleaseNotes($module);
            if ($notes === false) {
                continue;
            }

            $item = new Varien_Object();
            $item->setTitle(sprintf('%s &ndash; (Ver. <code>%s</code>)',
                    $module->getName(), $module->getVersion()));
            $item->setDescription($module->getDescription());
            $item->setText($notes);
            $rv->addItem($item);
        }

        return $rv;
     }

    private function _findReleaseNotes($module)
    {
        $config = $module->getConfig();
        $pool = (string) $config->codePool;
        $name = preg_replace('|_|', '/', $module->getModuleName());
        $path = sprintf('%s/%s/%s/docs/release-notes', Mage::getBaseDir('code'), $pool, $name);

        if (is_dir($path)) {
            $rv = "";
            foreach ($this->_concatenateFiles($path) as $rn) {
                $rv .= sprintf('<h3>Release notes %s &ndash; %s</h3>',
                    $rn->getVersion(),
                    $rn->getReleaseDate());
                $rv .= $rn->getText();
            }
        } else {
            return false;
        }
        return $rv;
    }

    private function _concatenateFiles($path)
    {
        $rv = new Varien_Data_Collection();
        $dh = opendir($path);
        $files = array();
        while (($file = readdir($dh)) !== false) {
            if (!is_file($path .'/' . $file)) {
                continue;
            }
            if (preg_match('|^\.|', $file)) {
                continue;
            }
            $files[] = $file;
        }
        asort($files);
        foreach ($files as $file) {
            $item = new Varien_Object();
            $item->setVersion(preg_replace('|(-\d{8})?.\w+$|', '', $file));
            $item->setText(file_get_contents($path . DS . $file));
            if (preg_match('|-\d{8}\.|', $file)) {
                $rDt = preg_replace('|.*-(\d{8})\..*|', '$1', $file);
            } else {
                $rDt = gmdate("Ymd", filemtime($path . DS . $file));
            }
            $item->setReleaseDate($this->formatDate($rDt));
            $rv->addItem($item);
        }
        closedir($dh);
        
        return $rv;
    }

    /**
     * Get platform version.
     * @return String
     */
    public function getPlatformVersion() {
        return Mage::helper('contactlab_commons')->getPlatformVersion();
    }
}
