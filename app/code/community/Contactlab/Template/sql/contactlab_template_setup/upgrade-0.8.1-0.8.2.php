<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$table = $installer->getTable('newsletter/queue_link');

$connection
    ->addColumn($table,
		'queued_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'comment' => 'Date of enqueue',
            'default' => 'CURRENT_TIMESTAMP'));

$installer->endSetup();
