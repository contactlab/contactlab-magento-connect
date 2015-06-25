<?php

/** Sample data controller. */
class Contactlab_Template_SampleDataController extends Mage_Core_Controller_Front_Action {
    public function createAction() {
        /* @var $helper Contactlab_Template_Helper_SampleData */
        $helper = Mage::helper('contactlab_template/sampleData');
        $helper->createSampleDataJSON();
        return $this->_redirect('/');
    }
}
