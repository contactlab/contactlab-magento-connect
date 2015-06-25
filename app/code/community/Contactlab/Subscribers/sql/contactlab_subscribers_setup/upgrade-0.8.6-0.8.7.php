<?php

$installer = $this;
$installer->startSetup();

// Create customer export table
$tableName = "contactlab_subscribers/uk";
$table = $installer->getTable($tableName);

// Alter subscribers table
$installer->getConnection()->addColumn($table,
		'is_exported',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Is exported'));

$installer->endSetup();
