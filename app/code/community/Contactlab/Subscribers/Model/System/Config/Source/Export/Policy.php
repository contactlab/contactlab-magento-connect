<?php

/**
 * Export policy model for configuration admin.
 */
class Contactlab_Subscribers_Model_System_Config_Source_Export_Policy {
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return array(
			array('value' => 0, 'label' => Mage::helper('contactlab_subscribers')->__('Always all subscribers')),
			array('value' => 1, 'label' => Mage::helper('contactlab_subscribers')->__('All subscribers only for the next export')),
			array('value' => 2, 'label' => Mage::helper('contactlab_subscribers')->__('Only modified subscribers since last export')),
		);
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			0 => Mage::helper('contactlab_subscribers')->__('Always all subscribers'),
			1 => Mage::helper('contactlab_subscribers')->__('All subscribers only for the next export'),
			2 => Mage::helper('contactlab_subscribers')->__('Only modified subscribers since last export'),
		);
	}

}
