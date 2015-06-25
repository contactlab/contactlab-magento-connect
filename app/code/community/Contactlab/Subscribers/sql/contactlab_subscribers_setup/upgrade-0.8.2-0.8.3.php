<?php


$installer = $this;

$subscribersTable = $installer->getTable('newsletter/subscriber');
$conn = $installer->getConnection();

// Alter subscribers table
$conn->addColumn($subscribersTable,
		'last_subscribed_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true
        ), 'Last subscribed at');
