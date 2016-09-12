<?php
abstract class Contactlab_Subscribers_Model_Exporter_Subscribers_MapTransporter_Abstract {
    protected $_map = array();
    protected $_is_mod = false;

    public function setMap($_map){
        $this->_map = $_map;
        $this->setIsMod();
    }

    public function getMap(){
        return $this->_map;
    }

    public function isMod(){
        return $this->_is_mod;
    }

    public function setIsMod($_is_mod = true){
        $this->_is_mod = $_is_mod;
    }
}