<?php

$installer = $this;
$installer->startSetup();

// Create customer export table
$installer->run("drop table if exists {$installer->getTable("contactlab_subscribers/customer_export")};");
$newTable = $installer->getConnection()
        ->newTable($installer->getTable("contactlab_subscribers/customer_export"))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity id')
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unique' => true,
            'nullable' => false,
        ), 'Email')
	    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
	        'unsigned'  => true,
	        ), 'Website Id')
        ->addColumn('export_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false
        ), 'Export date')
	    ->addIndex($installer->getIdxName('contactlab_subscribers/customer_export', array('email', 'website_id')),
	        array('email', 'website_id'))
	    ->addIndex($installer->getIdxName('contactlab_subscribers/customer_export', array('website_id')),
	        array('website_id'))
	    ->addForeignKey($installer->getFkName('contactlab_subscribers/customer_export', 'website_id', 'core/website', 'website_id'),
	        'website_id', $installer->getTable('core/website'), 'website_id',
	        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment("First email export date");

$installer->getConnection()->createTable($newTable);




// Create newsletter export table
$installer->run("drop table if exists {$installer->getTable("contactlab_subscribers/newsletter_subscriber_export")};");
$newTable = $installer->getConnection()
        ->newTable($installer->getTable("contactlab_subscribers/newsletter_subscriber_export"))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity id')
	    ->addColumn('subscriber_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	        'unsigned'  => true,
	        'nullable'  => false,
	        ), 'Subscriber Id')
        ->addColumn('export_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false
        ), 'Export date')
	    ->addIndex($installer->getIdxName('contactlab_subscribers/newsletter_subscriber_export', array('subscriber_id')),
	        array('subscriber_id'))
	    ->addForeignKey($installer->getFkName('contactlab_subscribers/newsletter_subscriber_export', 'subscriber_id', 'newsletter/subscriber', 'subscriber_id'),
	        'subscriber_id', $installer->getTable('newsletter/subscriber'), 'subscriber_id',
	        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment("First newsletter subscriber export date");

$installer->getConnection()->createTable($newTable);


$installer->endSetup();
