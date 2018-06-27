<?php

class Contactlab_Subscribers_Test_Helper_Checks extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Contactlab_Subscribers_Helper_Checks
     */
    private $helper;

    protected function setUp() {
        $this->helper = Mage::helper("contactlab_subscribers/checks");
    }


    /**
     * Get available checks.
     * @test
     */
    public function getAvailableChecks() {
        $essentials = $this->helper->getAvailableChecks(true);
        $all = $this->helper->getAvailableChecks();
        $this->assertInternalType('array', $essentials);
        $this->assertInternalType('array', $all);
        $this->assertNotEmpty($all);
        $this->assertNotEmpty($essentials);
        $this->assertGreaterThan(count($essentials), count($all));
        $this->checkCheckInstances($all);
        $this->checkCheckInstances($essentials);
    }

    /**
     * Run all available checks.
     * @test
     */
    public function runAvailableChecks() {
        $this->doTestRunAvailableChecks(true);
        $this->doTestRunAvailableChecks(false);
    }

    private function doTestRunAvailableChecks($bool) {
        $checks = $this->helper->runAvailableChecks($bool);
        $this->assertNotEmpty($checks);
        foreach ($checks as $check) {
            /* @var Contactlab_Subscribers_Model_Checks_CheckInterface $check */
            $this->assertInstanceOf('Contactlab_Subscribers_Model_Checks_CheckInterface', $check);
            if ($check->shouldFailInTest()) {
                $this->assertEquals($check->getExitCode(), Contactlab_Subscribers_Model_Checks_CheckInterface::ERROR,
                    $check->getName() . ' success');
            } else {
                $this->assertEquals($check->getExitCode(), Contactlab_Subscribers_Model_Checks_CheckInterface::SUCCESS,
                    $check->getName() . ' failed');
            }
        }
    }

    /**
     * Get last checks exit code.
     * @test
     */
    public function getLastExitCode() {
        $this->helper->runAvailableChecks(true);
        $this->assertEquals($this->helper->getLastExitCode(), Contactlab_Subscribers_Model_Checks_CheckInterface::ERROR);
    }

    /**
     * Run available essential checks and return last status.
     * @test
     */
    public function checkAvailableEssentialChecks() {
        $this->assertFalse($this->helper->checkAvailableEssentialChecks());
    }

    /**
     * Check Check instance.
     * @param $all
     */
    private function checkCheckInstances($all) {
        foreach ($all as $check) {
            /* @var Contactlab_Subscribers_Model_Checks_CheckInterface $check */
            $this->assertInstanceOf('Contactlab_Subscribers_Model_Checks_CheckInterface', $check);
        }
    }
}