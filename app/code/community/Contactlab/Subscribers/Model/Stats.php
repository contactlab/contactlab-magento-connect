<?php

/**
 * Exporter data helper.
 * @method Contactlab_Subscribers_Model_Stats setCustomerId($value)
 * @method setTotalOrdersAmount($value)
 * @method setTotalOrdersProducts($value)
 * @method setTotalOrdersCount($value)
 * @method setAvgOrdersAmount($value)
 * @method setAvgOrdersProducts($value)
 * @method setLastOrderDate($value)
 * @method setLastOrderAmount($value)
 * @method setLastOrderProducts($value)
 */
class Contactlab_Subscribers_Model_Stats extends Mage_Core_Model_Abstract {
    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/stats");
    }

    /**
     * Update customer statistics
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function updateFromCustomer(Mage_Customer_Model_Customer $customer) {
        $this->_updateLastOrder($customer);
        $this->_updateTotalOrder($customer);
        $this->_updateTotalOrderPeriod($customer, 1);
        $this->_updateTotalOrderPeriod($customer, 2);
        return $this;
    }

    /**
     * Update last order statistics.
     * @param Mage_Customer_Model_Customer $customer
     */
    private function _updateLastOrder(Mage_Customer_Model_Customer $customer) {
        /** @var $order Mage_Sales_Model_Order */
        $order = $this->_getLastOrder($customer);
        if ($order) {
            $this->setLastOrderDate($order->getCreatedAt());
            $this->setLastOrderAmount($order->getBaseGrandTotal());
            $this->setLastOrderProducts($order->getTotalItemCount());
        } else {
            $this->setLastOrderDate(null);
            $this->setLastOrderAmount(null);
            $this->setLastOrderProducts(null);
        }
    }

    /**
     * Update orders statistics for period.
     * @param Mage_Customer_Model_Customer $customer
     * @param $period
     */
    private function _updateTotalOrderPeriod(Mage_Customer_Model_Customer $customer, $period) {
        $periodLength = Mage::getStoreConfig("contactlab_subscribers/stats/period_" . $period);
        if (!is_numeric($periodLength) || $periodLength == 0) {
            return;
        }
        /* @var $coll Mage_Sales_Model_Resource_Order_Collection */
        $coll = Mage::getModel('sales/order')
                ->getCollection()
                ->addExpressionFieldToSelect('grand_total', 'sum({{base_grand_total}})', 'base_grand_total')
                ->addExpressionFieldToSelect('total_item_count', 'sum({{total_item_count}})', 'total_item_count')
                ->addExpressionFieldToSelect('order_count', 'sum({{1}})', '1')
                ->addAttributeToFilter("customer_id", $customer->getEntityId())
                ->addAttributeToFilter("status", array('neq' => Mage_Sales_Model_Order::STATE_CANCELED));
		$date = Mage::getModel('core/date');
        $coll->getSelect()->where("created_at >= adddate(date(?), interval -"
                . $periodLength . "  day)", $date->gmtDate());
        foreach ($coll as $sum) {
            $this->setData('period' . $period . '_amount', $sum->getGrandTotal());
            $this->setData('period' . $period . '_products', $sum->getTotalItemCount());
            $this->setData('period' . $period . '_orders', $sum->getOrderCount());
        }
    }

    /**
     * Update all orders statistics.
     * @param Mage_Customer_Model_Customer $customer
     */
    private function _updateTotalOrder(Mage_Customer_Model_Customer $customer) {
        /* @var $coll Mage_Sales_Model_Resource_Order_Collection */
        $coll = Mage::getModel('sales/order')
                ->getCollection()
                ->addExpressionFieldToSelect('grand_total', 'sum({{base_grand_total}})', 'base_grand_total')
                ->addExpressionFieldToSelect('total_item_count', 'sum({{total_item_count}})', 'total_item_count')
                ->addExpressionFieldToSelect('order_count', 'sum({{1}})', '1')
                ->addExpressionFieldToSelect('avg_grand_total', 'avg({{base_grand_total}})', 'base_grand_total')
                ->addExpressionFieldToSelect('avg_orders_products', 'avg({{total_item_count}})', 'total_item_count')
                ->addAttributeToFilter("status", array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
                ->addAttributeToFilter("customer_id", $customer->getEntityId());
        foreach ($coll as $sum) {
            $this->setTotalOrdersAmount($sum->getGrandTotal());
            $this->setTotalOrdersProducts($sum->getTotalItemCount());
            $this->setTotalOrdersCount($sum->getOrderCount());
            $this->setAvgOrdersAmount($sum->getAvgGrandTotal());
            $this->setAvgOrdersProducts($sum->getAvgOrdersProducts());
        }
    }

    /** Get last order of customer $customer. */
    private function _getLastOrder(Mage_Customer_Model_Customer $customer) {
        /* @var $coll Mage_Sales_Model_Resource_Order_Collection */
        $coll = Mage::getModel('sales/order')
                ->getCollection()
                ->setOrder('created_at', 'desc')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('base_grand_total')
                ->addAttributeToSelect('total_item_count')
                ->addAttributeToFilter("status", array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
                ->addAttributeToFilter("customer_id", $customer->getEntityId());
        $coll->getSelect()->limit(1);
        foreach ($coll as $order) {
            return $order;
        }
        return null;
    }
}
