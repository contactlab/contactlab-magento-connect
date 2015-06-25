<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$table = $installer->getTable('newsletter/queue');

$connection
    ->addColumn($table,
		'task_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Rif. task Contactlab'));

$connection
    ->addForeignKey(
        $installer->getFkName('newsletter/queue', 'task_id', 'contactlab_commons/task', 'task_id'),
        $table,
        'task_id',
        $installer->getTable('contactlab_commons/task'),
        'task_id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT, Varien_Db_Ddl_Table::ACTION_RESTRICT);


$table = $installer->getTable('newsletter/queue_link');
$connection
    ->addColumn($table,
		'nr_click', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'Number of click'));

$connection
    ->addColumn($table,
		'queued_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'comment' => 'Date of enqueue'));


$installer->endSetup();
