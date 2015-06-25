<?php

$installer = $this;
$installer->startSetup();

// Alter subscribers table
$installer->getConnection()
	->addColumn($installer->getTable('contactlab_commons/task_event'),
			'email_sent',
			array(
				'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
				'nullable' => false,
				'default' => 0,
				'comment' => 'Email alert sent'));
