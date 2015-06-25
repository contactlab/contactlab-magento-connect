<?php

$installer = $this;
$installer->startSetup();

// Alter subscribers table
$installer->getConnection()
    ->addColumn($installer->getTable('contactlab_commons/task'),
            'store_id',
            array(
                'unsigned'  => true,
                'default'   => '0',
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable' => false,
                'comment' => 'Store id'));

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'contactlab_commons/task',
        'store_id',
        'core/store',
        'store_id'
    ),
    $installer->getTable('contactlab_commons/task'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

