<?php

/**
 * Check Uk Policy.
 */
class Contactlab_Subscribers_Model_System_Config_Source_Export_CheckUkPolicy {
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return array(
			array('value' => 0, 'label' => Mage::helper('contactlab_subscribers')->__('No')),
			array('value' => 1, 'label' => Mage::helper('contactlab_subscribers')->__('Only check')),
			array('value' => 2, 'label' => Mage::helper('contactlab_subscribers')->__('Check and repair')),
		);
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			0 => Mage::helper('contactlab_subscribers')->__('No'),
			1 => Mage::helper('contactlab_subscribers')->__('Only check'),
			2 => Mage::helper('contactlab_subscribers')->__('Check and repair'),
		);
	}

}
