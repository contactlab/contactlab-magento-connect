<?php

$installer = $this;
$installer->startSetup();
$subscribersTable = $installer->getTable('newsletter/subscriber');
$installer->getConnection()
->addColumn($subscribersTable,'created_at', array(
		'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
		'nullable'  => true,
		'after'     => 'subscriber_confirm_code',
		'comment'   => 'Created At'
));
$installer->endSetup();