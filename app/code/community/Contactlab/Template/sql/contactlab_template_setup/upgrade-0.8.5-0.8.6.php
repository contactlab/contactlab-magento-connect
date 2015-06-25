<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('newsletter/template');

$connection
    ->addColumn($table,
		'is_test_mode', array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'comment' => 'Does the template is active for test sending?'));


$installer->endSetup();
