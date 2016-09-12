<?php
class Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Deleted extends Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Abstract{
    protected $_deleted = null;

    public function getDeleted(){
        return  $this->_deleted;
    }

    public function setDeleted($_deleted){
        $this->_deleted = $_deleted;
    }
}