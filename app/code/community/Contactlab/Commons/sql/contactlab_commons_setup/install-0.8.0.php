<?php

$installer = $this;
$installer->startSetup();

$installer->run("drop table if exists {$installer->getTable("contactlab_commons/task")};");
$taskTable = $installer->getConnection()
        ->newTable($installer->getTable("contactlab_commons/task"))
        ->addColumn('task_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Task Id')
        ->addColumn('task_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false
        ), 'Task Code')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default' => 'CURRENT_TIMESTAMP'
            ), 'Created At')
        ->addColumn('planned_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true
        ), 'Task planned at date time (can be null!)')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false
        ), 'Task description')
        ->addColumn('task_data', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true
        ), 'Task internal data')
        ->addColumn('number_of_retries', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'default' => '0'
        ), 'Number of retries')
        ->addColumn('max_retries', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'default' => '0'
        ), 'Max number of retries')
        ->addColumn('retries_interval', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'default' => '0'
        ), 'Minutes between retries')
        ->addColumn('model_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
            'nullable' => false
        ), 'Magento model name')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable' => false,
            'default' => 'new'
        ), 'Task status')
        ->addIndex($installer->getIdxName('contactlab_commons/task', array('status')),
            array('status'))
        ->setComment("Tasks");

$installer->getConnection()->createTable($taskTable);






$installer->run("drop table if exists {$installer->getTable("contactlab_commons/task_event")};");
$taskEventTable = $installer->getConnection()
        ->newTable($installer->getTable("contactlab_commons/task_event"))
        ->addColumn('task_event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Task Event Id')
        ->addColumn('task_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Task Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default' => 'CURRENT_TIMESTAMP'
            ), 'Created At')
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable'  => true,
            ), 'User id')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false
        ), 'Task event description')
        ->addColumn('task_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable' => false,
        ), 'Task status')
        ->addColumn('send_alert', Varien_Db_Ddl_Table::TYPE_BOOLEAN, NULL, array(
            'default' => '0',
        ), 'Show as alert')
        ->addForeignKey($installer->getFkName('contactlab_commons/task_event', 'task_id', 'contactlab_commons/task', 'task_id'),
            'task_id', $installer->getTable('contactlab_commons/task'), 'task_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('contactlab_commons/task_event', 'user_id', 'admin/user', 'user_id'),
            'user_id', $installer->getTable('admin/user'), 'user_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment("Task events");

$installer->getConnection()->createTable($taskEventTable);


$installer->run("drop table if exists {$installer->getTable("contactlab_commons/log")};");
$logTable = $installer->getConnection()
        ->newTable($installer->getTable("contactlab_commons/log"))
        ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Task Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
            'nullable'  => false,
            'default' => 'CURRENT_TIMESTAMP'
            ), 'Created At')
        ->addColumn('log_level', Varien_Db_Ddl_Table::TYPE_TINYINT, NULL, array(
            'nullable'  => false
            ), 'Log level')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false
        ), 'Task description')
        ->setComment("Logs");

$installer->getConnection()->createTable($logTable);

$installer->run("drop table if exists {$installer->getTable("contactlab_commons/deleted")};");
$logTable = $installer->getConnection()
->newTable($installer->getTable("contactlab_commons/deleted"))
->addColumn('deleted_entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, array(
		'identity' => true,
		'unsigned' => true,
		'nullable' => false,
		'primary' => true,
), 'Deleted Entity Id')
->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
		'nullable'  => false,
		'default' => 'CURRENT_TIMESTAMP'
), 'Created At')
->addColumn('model', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'  => false
), 'Model')
->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => false
), 'Email')
->addColumn('task_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, array(
		'nullable' => true
), 'Task Id')
->addForeignKey($installer->getFkName('contactlab_commons/deleted', 'task_id', 'contactlab_commons/task', 'task_id'),
		'task_id', $installer->getTable('contactlab_commons/task'), 'task_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
->setComment("Deleted customers and subscribers email log");

$installer->getConnection()->createTable($logTable);

$installer->endSetup();
