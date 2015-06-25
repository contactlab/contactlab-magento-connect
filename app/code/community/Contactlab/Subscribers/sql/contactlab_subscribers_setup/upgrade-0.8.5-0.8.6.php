<?php


$installer = $this;

$table = $installer->getTable('contactlab_subscribers/customer_export');
$conn = $installer->getConnection();

// Alter subscribers table
$conn->addColumn($table,
		'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'unsigned'  => true
        ), 'Customer Id');
$installer->run("update $table set customer_id = (select e.entity_id from customer_entity e where e.email = $table.email)");
$installer->run("delete from $table where customer_id is null");

$conn->addForeignKey(
    $installer->getFkName('contactlab_subscribers/customer_export', 'customer_id', 'customer/entity', 'entity_id'),
    $table, 
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
