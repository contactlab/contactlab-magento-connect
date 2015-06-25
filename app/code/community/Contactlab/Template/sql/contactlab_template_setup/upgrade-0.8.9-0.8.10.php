<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$tableName = $installer->getTable('newsletter/queue_link');
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$connection->addColumn($tableName, 'customer_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => true,
    'comment' => 'Customer id'
));

$connection->addForeignKey(
        $installer->getFkName('newsletter/queue_link',
                'customer_id', 'customer/entity',
                'entity_id'),
        $tableName, 'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_SET_NULL);

$connection->changeColumn($tableName, "subscriber_id", "subscriber_id", array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'default'   => '0',
    'comment'   => 'Subscriber Id'
));

$installer->endSetup();
