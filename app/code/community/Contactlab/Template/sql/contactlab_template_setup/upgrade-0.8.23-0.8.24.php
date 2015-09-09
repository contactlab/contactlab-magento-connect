<?php

$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();

$tableName = $installer->getTable('newsletter/template');

$connection->addColumn($tableName, 'store_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => true,
    'comment' => 'Store id'
));

$connection->addForeignKey(
    $installer->getFkName('newsletter/template',
        'store_id', 'core/store',
        'store_id'),
    $tableName, 'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->endSetup();
