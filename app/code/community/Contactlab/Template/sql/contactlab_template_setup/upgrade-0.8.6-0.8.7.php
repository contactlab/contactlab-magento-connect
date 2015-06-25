<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('newsletter/template');

$connection
    ->addColumn($table,
		'product_image_size', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'length' => 32,
            'comment' => 'Product image size'));


$installer->endSetup();
