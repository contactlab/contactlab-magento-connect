<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('newsletter/template');

$connection
    ->addColumn($table,
		'priority', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Priority'));

$installer->endSetup();
