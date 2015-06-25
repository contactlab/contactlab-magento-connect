<?php

$installer = $this;
$installer->startSetup();

// Create customer export table
$table = "contactlab_subscribers/uk";
$tableName = $installer->getTable($table);

$installer->run("drop table if exists $tableName;");
$newTable = $installer->getConnection()
        ->newTable($installer->getTable($table))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'ContactLab Entity id')
        ->addColumn('subscriber_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            ), 'Subscriber Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            ), 'Customer Id')
        ->addIndex($installer->getIdxName($table, array('subscriber_id')), array('subscriber_id'))
        ->addForeignKey($installer->getFkName($table, 'subscriber_id', 'newsletter/subscriber', 'subscriber_id'),
            'subscriber_id', $installer->getTable('newsletter/subscriber'), 'subscriber_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_RESTRICT)
        ->addIndex($installer->getIdxName($table, array('customer_id')), array('customer_id'))
        ->addForeignKey($installer->getFkName($table, 'customer_id', 'customer/entity', 'entity_id'),
            'customer_id', $installer->getTable('customer/entity'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_RESTRICT)
        ->setComment("Real ContactLab Id Table");

$installer->getConnection()->createTable($newTable);

$installer->endSetup();
