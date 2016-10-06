<?php

class Contactlab_Commons_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Contactlab_Commons_Helper_Data
     */
    private $helper;

    protected function setUp()
    {
        $this->helper = Mage::helper("contactlab_commons");
    }

    /**
     * @test
     */
    public function isDebug() {
        $isDebug = $this->helper->isDebug();

        $this->assertFalse($isDebug, "Debug mode should be disabled by default");
    }

    /**
     * @test
     */
    public function getPlatformVersion() {
        $version = $this->helper->getPlatformVersion();
        $this->assertRegExp('|\d+\.\d+(\.\d+)?|', $version, "Platform version should be x.x(.x)?");
    }

    /**
     * @test
     */
    public function logEmerg() {
        $this->checkLog("Emerg");
    }

    /**
     * @test
     */
    public function logAlert() {
        $this->checkLog("Alert");
    }

    /**
     * @test
     */
    public function logCrit() {
        $this->checkLog("Crit");
    }

    /**
     * @test
     */
    public function logErr() {
        $this->checkLog("Err");
    }

    /**
     * @test
     */
    public function logWarn() {
        $this->checkLog("Warn");
    }

    /**
     * @test
     */
    public function logNotice() {
        $this->checkLog("Notice");
    }

    /**
     * @test
     */
    public function logDebug() {
        $this->checkLog("Debug");
    }

    /**
     * @test
     */
    public function enableDbProfiler()
    {
        $this->helper->enableDbProfiler();
        /**  @var $connection Varien_Db_Adapter_Pdo_Mysql */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $profiler = $connection->getProfiler();
        $profiler->setEnabled(true);
        $this->assertTrue($profiler->getEnabled());
        $tasks = Mage::getModel('contactlab_commons/task')->getCollection();
        $tasks->count();
        $found = false;

        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        foreach ($profiler->getQueryProfiles() as $q) {
            /** @var $q Zend_Db_Profiler_Query */
            if (strpos($q->getQuery(), $tablePrefix.'contactlab_commons_task_entity')) {
                $found = true;
            }
        }
        $this->assertTrue($found, "Query not profiled");
    }

    /**
     * @test
     * @depends enableDbProfiler
     */
    public function flushDbProfiler() {
    }

    /**
     * @test
     */
    public function isAllowed() {
        $this->assertFalse($this->helper->isAllowed('commons', 'tasks'));
    }

    /**
     * @test
     */
    public function isMageSameOrNewerOf() {
        $this->assertTrue($this->helper->isMageSameOrNewerOf(1, 7));
        $this->assertFalse($this->helper->isMageSameOrNewerOf(1, 11));
    }

    /**
     * @test
     */
    public function deleteFromSelect() {
    }

    /**
     * @test
     */
    public function getModulesVersion() {
    }

    /**
     * @test
     */
    public function logCronCall()
    {
    }

    /**
     * @test
     */
    public function logInfo() {
    }

    /**
     * @param $level
     */
    private function checkLog($level)
    {
        $random = $this->generateRandomString(64);
        $f = "log${level}";;
        $this->helper->$f($random);
        $logFile = Mage::getBaseDir('log') . DS . 'contactlab.log';
        $this->assertTrue($this->findLogEntry($logFile, $random, $level), "Could not log with level $level");
    }

    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function findLogEntry($logFile, $random, $level)
    {
        $log = exec("grep $random $logFile");
        return strpos($log, $level) !== false;
    }
}