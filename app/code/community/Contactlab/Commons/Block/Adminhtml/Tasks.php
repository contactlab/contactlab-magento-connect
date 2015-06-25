<?php

/**
 * Tasks block.
 */
class Contactlab_Commons_Block_Adminhtml_Tasks extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Construct the block.
     */
    public function __construct() {
        $this->_blockGroup = 'contactlab_commons';
        $this->_controller = 'adminhtml_tasks';
        $this->_headerText = $this->__("Tasks");

        parent::__construct();
        $this->removeButton("add");
		$this->addCommandButton("tasks", "clear", 'contactlab_commons', "Clear old tasks",
				'Are you sure you want to do this?', '*/*/clear');
		$this->addCommandButton("tasks", "clear", 'contactlab_commons', "Clear old tasks",
				'Are you sure you want to do this?', '*/*/clear');
		if (Mage::helper('contactlab_commons')->isDebug()) {
			$this->addCommandButton("tasks", "consume", 'contactlab_commons', "Consume tasks",
					null, '*/*/consume');
			$this->addCommandButton("uk", "check", 'contactlab_subscribers', "Check unique Keys",
					null, 'contactlab_subscribers/adminhtml_uk/update');
			$this->addCommandButton("uk", "update", 'contactlab_subscribers', "Update unique Keys",
					null, 'contactlab_subscribers/adminhtml_uk/update', array('doit' => 'yes'));
            if (Mage::helper('core')->isModuleEnabled('Contactlab_Template')) {
    			$this->addCommandButton("template", "scan", 'contactlab_template', "Scan templates",
	    				null, 'contactlab_template/adminhtml_template/scan');
            }
		}
    }

    /** Add command button from xml layout. */
    public function addCommandButton($section, $code, $helper, $label, $alert, $url, $params = array()) {
    	if (!Mage::helper('contactlab_commons')->isAllowed($section, $code)) {
    		return;
    	}
        $h = Mage::helper($helper);
		if (is_null($alert)) {
			$onclick = 'location.href = \'' . Mage::helper('adminhtml')->getUrl($url, $params) . '\'';
		} else {
			$onclick = 'deleteConfirm(\'' . $h->__($alert)
                . '\', \'' . Mage::helper('adminhtml')->getUrl($url, $params) . '\')';
		}
        $this->addButton($code, array(
            'label' => $h->__($label),
            'onclick' => $onclick,
        ));
    }
}
