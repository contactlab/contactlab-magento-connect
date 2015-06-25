<?php

$installer = $this;
$installer->startSetup();

// Create customer export table
$table = "contactlab_subscribers/uk";
$tableName = $installer->getTable($table);

$subscribers = "newsletter/subscriber";
$subscribersTable = $installer->getTable($subscribers);

$customers = "customer/entity";
$customersTable = $installer->getTable($customers);

$installer->run("insert into $tableName (subscriber_id, customer_id) select subscriber_id, if(customer_id = 0, NULL, customer_id) from $subscribersTable");
$installer->run("insert into $tableName (customer_id) select entity_id from $customersTable where entity_id not in (select customer_id from $subscribersTable)");

$installer->endSetup();
