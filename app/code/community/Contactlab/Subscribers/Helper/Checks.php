<?php

class Contactlab_Subscribers_Helper_Checks extends Mage_Core_Helper_Abstract
{
    private $_lastChecks = array();

    /**
     * Get available checks.
     * @param bool $onlyEssentials
     * @return array
     */
    public function getAvailableChecks($onlyEssentials = false)
    {
        $checkDir = __DIR__ . '/../Model/Checks';
        $rv = array();
        foreach (scandir($checkDir) as $file) {
            if (!preg_match('|\.php$|', $file)) {
                continue;
            }
            $className = preg_replace('|\.php$|', '', $file);
            if ($className === 'AbstractCheck' || $className === 'CheckInterface') {
                continue;
            }
            $modelName = 'contactlab_subscribers/checks_'
                . strtolower(substr($className, 0, 1))
                . substr($className, 1);
            /* @var $instance Contactlab_Subscribers_Model_Checks_CheckInterface */
            $instance = Mage::getModel($modelName);
            if ($onlyEssentials && !$instance->isEssential()) {
                continue;
            }
            $rv[] = $instance;
        }
        usort($rv, function ($a, $b) {
            /* @var $a Contactlab_Subscribers_Model_Checks_CheckInterface */
            /* @var $b Contactlab_Subscribers_Model_Checks_CheckInterface */
            $a = $a->getPosition();
            $b = $b->getPosition();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        return $rv;
    }

    /**
     * Run all available checks.
     * @param bool $onlyEssentials
     * @return array
     */
    public function runAvailableChecks($onlyEssentials = false)
    {
        $this->_lastChecks = array();
        $checks = $this->getAvailableChecks($onlyEssentials);
        /* @var $check Contactlab_Subscribers_Model_Checks_CheckInterface */
        foreach ($checks as $check) {
            $this->_lastChecks[] = $check;
            $check->check();
        }
        return $this->_lastChecks;
    }

    /**
     * Get last checks exit code.
     * @return string
     */
    public function getLastExitCode()
    {
        /* @var $check Contactlab_Subscribers_Model_Checks_CheckInterface */
        foreach ($this->_lastChecks as $check) {
            if ($check->getExitCode() === Contactlab_Subscribers_Model_Checks_CheckInterface::ERROR) {
                return $check->getExitCode();
            }
        }
        return Contactlab_Subscribers_Model_Checks_CheckInterface::SUCCESS;
    }

    /**
     * Run available essential checks and return last status.
     * @return bool
     */
    public function checkAvailableEssentialChecks() {
        $this->runAvailableChecks(true);
        return $this->getLastExitCode() === Contactlab_Subscribers_Model_Checks_CheckInterface::SUCCESS;
    }

    /**
     * Throw Last Check Exception.
     * @param Contactlab_Commons_Model_Task $task
     * @return Exception
     */
    public function getLastCheckException(Contactlab_Commons_Model_Task $task) {
        $msg = array();
        /* @var $check Contactlab_Subscribers_Model_Checks_CheckInterface */
        foreach ($this->_lastChecks as $check) {
            if ($check->getExitCode() === Contactlab_Subscribers_Model_Checks_CheckInterface::ERROR) {
                foreach ($check->getErrors() as $error) {
                    $task->addEvent($error, true);
                    $msg[] = $error;
                }
            }
        }
        return new Exception(implode('<br>', $msg));
    }
}