<?php

class Contactlab_Template_Model_Resource_Newsletter_Queue_Link extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('newsletter/queue_link', 'queue_link_id');
    }

}