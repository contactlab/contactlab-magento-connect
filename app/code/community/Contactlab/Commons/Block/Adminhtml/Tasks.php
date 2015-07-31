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
		$this->addCommandButton("tasks", "clear", 'contactlab_commons', "Clear old tasks", 'Are you sure you want to do this?', '*/*/clear');
		if (Mage::helper('contactlab_commons')->isDebug()) {
			$this->addCommandButton("tasks", "consume", 'contactlab_commons', "Run tasks",
					null, '*/*/consume');
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
