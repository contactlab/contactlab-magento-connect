<?php

class Contactlab_Commons_Model_Resource_Deleted extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {
		$this->_init ('contactlab_commons/deleted', 'deleted_entity_id' );
	}
}
