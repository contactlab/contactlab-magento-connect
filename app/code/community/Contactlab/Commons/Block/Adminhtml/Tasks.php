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
			$this->addCommandButton("uk", "truncate", 'contactlab_subscribers', "Truncate unique Keys",
					null, 'contactlab_subscribers/adminhtml_uk/truncate');
            if (Mage::helper('core')->isModuleEnabled('Contactlab_Template')) {
                $this->addEmailAddressDebug();
                $url = 'contactlab_template/adminhtml_template/scan';
                $onClick = 'var v = $(\'email-address-debug\').value; location.href = \'' . Mage::helper('adminhtml')->getUrl($url) . 'address/\' + v';
    			$this->addCommandButton("template", "scan", 'contactlab_template', "Scan templates",
	    				null, $url, array(), $onClick);
            }
		}
    }

    /** Add command button from xml layout. */
    public function addCommandButton($section, $code, $helper, $label, $alert, $url, $params = array(), $onclickParam = null) {
    	if (!Mage::helper('contactlab_commons')->isAllowed($section, $code)) {
    		return;
    	}
        $h = Mage::helper($helper);
        if (!is_null($onclickParam)) {
            $onclick = $onclickParam;
        } else if (is_null($alert)) {
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

    public function addEmailAddressDebug() {
        $block = Mage::app()->getLayout()
                ->createBlock('contactlab_template/adminhtml_tasks_debugEmail');
        $block->setAddress(Mage::app()->getRequest()->getParam('address'));
        $this->setChild("emailAddressDebug", $block);
    }
    
    public function getButtonsHtml($area = null) {
        if ($this->getChild('emailAddressDebug')) {
            return parent::getButtonsHtml($area) . $this->getChild('emailAddressDebug')->toHtml();
        } else {
            return parent::getButtonsHtml($area);
        }
    }
}
