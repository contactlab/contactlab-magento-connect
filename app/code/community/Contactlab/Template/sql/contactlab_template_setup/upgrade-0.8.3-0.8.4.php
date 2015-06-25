<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('newsletter/queue_link');

$connection
    ->addColumn($table,
		'product_ids', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'length' => 255,
            'comment' => 'Product Ids'));

$installer->endSetup();
