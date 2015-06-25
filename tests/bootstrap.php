<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author andreag
 */
// TODO: check include path
ini_set('include_path',
        ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . '/../../../../../usr/share/php'
        . PATH_SEPARATOR . dirname(__FILE__) . '/../app/code/core');

// put your code here

require_once(dirname(__FILE__) . '/../app/Mage.php');
//Mage::setIsDeveloperMode(true);
Mage::app('admin')->setUseSessionInUrl(false);
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(1);
Mage::getSingleton('admin/session')->setUser($userModel);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

Mage::log("Bootstrap tests", null, "unit-test.log", true);

require_once(dirname(__FILE__) . '/unit/Webformat/PHPUnit/Framework/AbstractMagentoTestCase.php');

class Remover extends Webformat_PHPUnit_Framework_TestCase_AbstractMagentoTestCase {
}
/* @var $testCase Remover */
$testCase = new Remover();
$testCase->removeTestCustomers();

/* Undo Magento's hiding of errors */
//error_reporting(-1);
//ini_set('display_errors', 1);
