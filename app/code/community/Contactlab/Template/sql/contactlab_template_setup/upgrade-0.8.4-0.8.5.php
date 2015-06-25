<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('contactlab_template/type');

$connection
    ->addColumn($table,
		'dnd_period', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Dnd period in days'));
$connection
    ->addColumn($table,
		'dnd_mail_number', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Dnd email numbers'));

$installer->endSetup();
