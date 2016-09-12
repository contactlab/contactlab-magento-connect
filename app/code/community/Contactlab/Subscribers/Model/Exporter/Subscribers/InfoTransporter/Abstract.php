<?php
abstract class Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Abstract {
    protected $_info = array();
    protected $_is_mod = false;

    public function setInfo($_info){
        $this->_info = $_info;
        $this->setIsMod();
    }

    public function getInfo(){
        return $this->_info;
    }

    public function isMod(){
        return $this->_is_mod;
    }

    public function setIsMod($_is_mod = true){
        $this->_is_mod = $_is_mod;
    }
}