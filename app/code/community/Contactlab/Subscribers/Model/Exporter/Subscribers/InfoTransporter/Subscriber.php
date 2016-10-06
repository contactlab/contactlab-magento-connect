<?php
class Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Subscriber extends Contactlab_Subscribers_Model_Exporter_Subscribers_InfoTransporter_Abstract{
    protected $_subscriber = null;

    public function getSubscriber(){
        return  $this->_subscriber;
    }

    public function setSubscriber($_subscriber){
        $this->_subscriber = $_subscriber;
    }
}