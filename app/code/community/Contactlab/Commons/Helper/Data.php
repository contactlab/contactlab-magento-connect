<?php

/**
 * Helper data for logging purpose.
 */
class Contactlab_Commons_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Log levels.
     */
    static $LEVELS = array(
        Zend_Log::EMERG => "Emerg",
        Zend_Log::ALERT => "Alert",
        Zend_Log::CRIT => "Crit",
        Zend_Log::ERR => "Err",
        Zend_Log::WARN => "Warn",
        Zend_Log::NOTICE => "Notice",
        Zend_Log::INFO => "Info",
        Zend_Log::DEBUG => "Debug"
    );


    /** Constructor. */
    public function __construct() {
        $this->_mageVersion = Mage::getVersionInfo();
        $this->_mageMajor = $this->_mageVersion['major'];
        $this->_mageMinor = $this->_mageVersion['minor'];
        $this->_mageRevision = $this->_mageVersion['revision'];
    }

    /**
     * Get platform version.
     * @return String
     */
    public function getPlatformVersion() {
        return Mage::getStoreConfig('contactlab_commons/global/platform_version');
    }

    /**
     * Emergency: system is unusable
     * @param string $value
     */
    public function logEmerg($value) {
        $this->_log($value, Zend_Log::EMERG);
    }

    /**
     * Alert: action must be taken immediately
     * @param string $value
     */
    public function logAlert($value) {
        $this->_log($value, Zend_Log::ALERT);
    }

    /**
     * Critical: critical conditions
     * @param string $value
     */
    public function logCrit($value) {
        $this->_log($value, Zend_Log::CRIT);
    }

    /**
     * Error: error conditions
     * @param string $value
     */
    public function logErr($value) {
        $this->_log($value, Zend_Log::ERR);
    }

    /**
     * Warning: warning conditions
     * @param string $value
     */
    public function logWarn($value) {
        $this->_log($value, Zend_Log::WARN);
    }

    /**
     * Notice: normal but significant condition
     * @param string $value
     */
    public function logNotice($value) {
        $this->_log($value, Zend_Log::NOTICE);
    }

    /**
     * Informational: informational messages
     * @param string $value
     */
    public function logInfo($value) {
        $this->_log($value, Zend_Log::INFO);
    }

    /**
     * Debug: debug messages
     * @param string $value
     */
    public function logDebug($value) {
        $this->_log($value, Zend_Log::DEBUG);
    }

    /**
     * Private log function.
     * @param string $value
     * @param int $level
     */
    private function _log($value, $level) {
        Mage::log(sprintf("%-10s %-6s - %s", "Global",
                self::$LEVELS [$level], $value), null, "contactlab.log", true);
        Mage::getModel("contactlab_commons/log")
                ->setDescription($value)->setLogLevel($level)->save();
    }

    /**
     * Check if debug is enabled.
     */
    public function isDebug() {
        return Mage::getStoreConfigFlag("contactlab_commons/global/debug");
    }

    /**
     * Enable Zend DB Profiler.
     */
    public function enableDbProfiler() {
        if (!$this->isDebug()) {
            return;
        }
        /** @var $connection Varien_Db_Adapter_Pdo_Mysql */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->getProfiler()->setEnabled(true);
        Varien_Profiler::enable();
    }

    /**
     * Flush Zend DB Profiler.
     */
    public function flushDbProfiler() {
        if (!$this->isDebug()) {
            return;
        }
        /** @var $connection Varien_Db_Adapter_Pdo_Mysql */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $profiler = $connection->getProfiler();

        $csvArray = array();
        foreach ($profiler->getQueryProfiles() as $q) {
            /** @var $q Zend_Db_Profiler_Query */
            $csvArray[] = array($q->getElapsedSecs(), $q->getQuery());
        }
        $logDir  = Mage::getBaseDir('var') . DS . 'log';
        $fp = fopen($logDir . DS . "profiler.csv", 'w');
        foreach ($csvArray as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }

    /**
     * Is the action $action allowed?
     * @param string $section
     * @param string $action
     * @return bool
     */
    public function isAllowed($section, $action) {
        if (php_sapi_name() === 'cli') {
            return false;
        }
        return Mage::getSingleton('admin/session')
        	->isAllowed("admin/newsletter/contactlab/$section/actions/$action");
    }

    /**
     * Mage same or newer of (for backward compatibility).
     * @param int $major
     * @param int $minor
     * @param int $revision
     * @return bool
     */
    public function isMageSameOrNewerOf($major, $minor, $revision = 0) {
        if ($this->_mageMajor < $major) {
            return false;
        } else if ($this->_mageMinor < $minor) {
            return false;
        } else if ($this->_mageRevision < $revision) {
            return false;
        }
        return true;
    }

    /**
     * For backward compatibility.
     * @param Varien_Db_Adapter_Pdo_Mysql $adapter
     * @param Zend_Db_Select $select
     * @param $table
     * @return string
     */
    public function deleteFromSelect(Varien_Db_Adapter_Pdo_Mysql $adapter, Zend_Db_Select $select, $table) {
        $select = clone $select;
        $select->reset(Zend_Db_Select::DISTINCT);
        $select->reset(Zend_Db_Select::COLUMNS);

        return sprintf('DELETE %s %s', $adapter->quoteIdentifier($table), $select->assemble());
    }

    /**
     * Get module versions.
     * @return \Varien_Data_Collection
     */
    public function getModulesVersion() {
        $rv = new Varien_Data_Collection();
        $count = 0;
        foreach (Mage::getConfig()->getNode('modules')->children() as $moduleName => $moduleConfig) {
            if (preg_match('/^Contactlab_.*/', $moduleName)) {
                if (((string) $moduleConfig->active) === 'false') {
                    continue;
                }
                $item = new Varien_Object();
                $item->setName(preg_replace('/^Contactlab_/', '', $moduleName))
                    ->setVersion((string) $moduleConfig->version)
                    ->setConfig($moduleConfig)
                    ->setModuleName($moduleName)
                    ->setDescription((string) $moduleConfig->description);
                if ($count++ % 2 == 0) {
                    $item->setClass("even");
                }
                $rv->addItem($item);
            }
        }
        return $rv;
    }

    /**
     * Log function call.
     * @param String $functionName
     * @param bool|String $storeId
     */
    public function logCronCall($functionName, $storeId = false)
    {
        $pid = getmypid();
        $uid = getmyuid();
        $sapi = php_sapi_name();
        if (!is_string($functionName)) {
            try {
                $functionName = strval($functionName);
            } catch (\Exception $e) {
                $functionName = is_object($functionName)?get_class($functionName):gettype($functionName);
            }
        }
        if ($storeId !== false && !is_string($storeId)) {
            try {
                $storeId = strval($storeId);
            } catch (\Exception $e) {
                $storeId = is_object($storeId)?get_class($storeId):gettype($storeId);
            }
        }

        if ($storeId !== false) {
            $this->logInfo(sprintf(
                "Function %s called. pid: %s, uid: %s, sapi: %s, store: %s.",
                $functionName, $pid, $uid, $sapi, $storeId));
        } else {
            $this->logInfo(sprintf(
                "Function %s called. pid: %s, uid: %s, sapi: %s.",
                $functionName, $pid, $uid, $sapi));
        }
    }

    /**
     * Add critical message.
     * @param string$value
     */
    public function addCriticalMessage($value) {
        if ($this->_useCoreAddCritical()) {
            Mage::getModel('adminnotification/inbox')->addCritical($value, "Contactlab");
        } else {
            $this->_addCritical($value, "Contactlab");
        }
    }

    /** For Mage 1.7 or newer. */
    private function _useCoreAddCritical() {
        return Mage::helper("contactlab_commons")->isMageSameOrNewerOf(1, 7);
    }

    /**
     * Add new message (Back porting)
     *
     * @param string $title
     * @param string $description
     * @return $this
     */
    private function _addCritical($title, $description) {
        $t = Mage::getModel('adminnotification/inbox');
        $date = date('Y-m-d H:i:s');
        $t->parse(array(array(
            'severity'    => Mage::helper('adminnotification')->__('critical'),
            'date_added'  => $date,
            'title'       => $title,
            'description' => $description,
            'url'         => "",
            'internal'    => true
        )));
        return $this;
    }
}
