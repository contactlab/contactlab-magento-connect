<?php

$installer = $this;
$installer->startSetup ();

// Create stats table
$installer->run("drop table if exists {$installer->getTable("contactlab_subscribers/stats")};");
$subscribersTable = $installer->getConnection()
        ->newTable($installer->getTable("contactlab_subscribers/stats"))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'unique' => true,
        ), 'Customer id')
        ->addColumn('last_order_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true
        ), 'Last order date')
        ->addColumn('last_order_amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'Last order amount')
        ->addColumn('last_order_products', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Nr of products in last order')
        ->addColumn('total_orders_amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'Total orders amount')
        ->addColumn('total_orders_products', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Total nr of products')
        ->addColumn('total_orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Total nr of orders')
        ->addColumn('avg_orders_amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'Avg order amount')
        ->addColumn('avg_orders_products', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'Avg number of products')
        ->addColumn('period1_amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'Order amount in period 2')
        ->addColumn('period1_products', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Nr of products in period 1')
        ->addColumn('period1_orders', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Nr of orders in period 1')
        ->addColumn('period2_amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'Order amount in period 2')
        ->addColumn('period2_products', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Nr of products in period 2')
        ->addColumn('period2_orders', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true
        ), 'Nr of orders in period 2')
        ->addForeignKey($installer->getFkName('contactlab_subscribers/stats', 'customer_id', 'customer/entity', 'entity_id'),
            'customer_id', $installer->getTable('customer/entity'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment("Subscribers stats");

$installer->getConnection()->createTable($subscribersTable);

$installer->endSetup();
