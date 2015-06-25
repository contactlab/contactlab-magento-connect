<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();


$templateTable = $installer->getTable('newsletter/template');

$connection
    ->addColumn($templateTable,
		'reply_to', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Reply to',
            'length' => 255));

$installer->endSetup();
