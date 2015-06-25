<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('contactlab_commons/deleted'),
            'is_customer',
            array(
                'unsigned'  => true,
                'default'   => '0',
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable' => false,
                'comment' => 'Is customer'));
$installer->getConnection()
    ->addColumn($installer->getTable('contactlab_commons/deleted'),
            'entity_id',
            array(
                'unsigned'  => true,
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Entity id'));
