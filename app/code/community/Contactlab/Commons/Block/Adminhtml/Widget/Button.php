<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Button widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Contactlab_Commons_Block_Adminhtml_Widget_Button extends Mage_Adminhtml_Block_Widget_Button
{
    protected function _construct()
    {
        parent::_construct();
        $this->setParams("");
    }

    /**
     * Set url.
     * @param type $url
     * @param type $params
     */
    public function setUrl($url, $params = "") {
        parent::setButtonUrl($url);
        $this->setParams($params);
    }

    public function getOnClick() {
        $url = $this->getButtonUrl();
        $params = array();
        parse_str($this->getParams(), $params);
        if ($this->hasConfirm()) {
			$onclick = 'deleteConfirm(\'' . $this->getConfirm()
                . '\', \'' . Mage::helper('adminhtml')->getUrl($url, $params) . '\')';
		} else {
            $onclick = 'location.href = \'' . Mage::helper('adminhtml')->getUrl($url, $params) . '\'';
		}
        return $onclick;
    }
}
