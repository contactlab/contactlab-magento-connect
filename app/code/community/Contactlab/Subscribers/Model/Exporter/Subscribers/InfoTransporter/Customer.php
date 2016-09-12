<?php
class Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Customer extends Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Abstract{
    protected $_customer = null;

    public function getCustomer(){
        return  $this->_customer;
    }

    public function setCustomer($_customer){
        $this->_customer = $_customer;
    }
}