<?php

$installer = $this;
$installer->startSetup();

// Alter subscribers table
$installer->getConnection()
	->addColumn($installer->getTable('contactlab_commons/task'),
			'max_value',
			array(
				'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
				'nullable' => true,
				'comment' => 'Task progress max value'));
$installer->getConnection()
    ->addColumn($installer->getTable('contactlab_commons/task'),
            'progress_value',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'nullable' => true,
                'comment' => 'Task progress value'));
