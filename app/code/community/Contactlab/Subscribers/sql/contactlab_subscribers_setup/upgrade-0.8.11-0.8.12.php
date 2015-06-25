<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = "contactlab_subscribers/uk";
$tableName = $installer->getTable($table);

$installer->getConnection()->dropIndex($tableName, 'subscriber_id');
$installer->getConnection()->dropIndex($tableName, 'subscriber_id_2');

$installer->getConnection()->addIndex($tableName,
        $installer->getConnection()->getIndexName($tableName, array('customer_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE), array('customer_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);

$installer->getConnection()->addIndex($tableName,
        $installer->getConnection()->getIndexName($tableName, array('subscriber_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE), array('subscriber_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);

$installer->endSetup();