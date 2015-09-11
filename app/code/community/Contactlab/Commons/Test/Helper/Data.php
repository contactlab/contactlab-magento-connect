<?php

class Contactlab_Commons_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     *
     * @return bool
     */
    public function isDebug() {
        $isDebug = Mage::getStoreConfigFlag("contactlab_commons/global/debug");

        $this->assertFalse($isDebug);
    }
}