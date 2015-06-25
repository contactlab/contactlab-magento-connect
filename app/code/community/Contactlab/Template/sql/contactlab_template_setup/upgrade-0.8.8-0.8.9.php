<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('newsletter/template');


$connection
    ->addColumn($table,
		'min_minutes_from_last_update', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Minimum number of minutes'));

$connection
    ->addColumn($table,
		'max_minutes_from_last_update', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Maximum number of minutes'));

$connection
    ->addColumn($table,
		'min_value', array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'comment' => 'Minimum value'));

$connection
    ->addColumn($table,
		'max_value', array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'comment' => 'Maximum value'));

$connection
    ->addColumn($table,
		'min_products', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Minimum number of products'));

$connection
    ->addColumn($table,
		'max_products', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Maximum number of products'));

$connection
    ->addColumn($table,
		'and_or', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 16,
            'nullable' => false,
            'default' => 'AND',
            'comment' => 'Number and value conditions in and/or?'));


$installer->endSetup();
