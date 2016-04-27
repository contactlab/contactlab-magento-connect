<?php

$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

// Create subscriber fields table
$table = "contactlab_subscribers/newsletter_subscriber_fields";
$tableName = $installer->getTable($table);

/** @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();

if (!$connection->isTableExists($tableName)) {
    $newTable = $connection->newTable($tableName)
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true), 'ContactLab Entity id')
        ->addColumn('subscriber_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array('nullable' => true),
            'Newsletter subscribers table entity Id')
        ->addColumn('subscriber_email',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            150, array(
                'nullable' => true,
                'default' => null),
            'Subscriber Email')
        ->addColumn('first_name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array('nullable' => true),
            'First name')
        ->addColumn('last_name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array('nullable' => true),
            'Last name')
        ->addColumn('company',
            Varien_Db_Ddl_Table::TYPE_TEXT, 255,
            array('nullable' => true), 'Company')
        ->addColumn('gender',
            Varien_Db_Ddl_Table::TYPE_TEXT, 10,
            array('nullable' => true), 'Gender')
        ->addColumn('dob',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null, array('nullable' => true), 'Date of birth')
        ->addColumn('privacy_accepted',
            Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            null, array('nullable' => false), 'Privacy acceptance flag')
        ->addColumn('custom_1',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array('nullable' => true), 'Custom text field #1')
        ->addColumn('custom_2',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array('nullable' => true), 'Custom text field #2')
        ->addColumn('country',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array('nullable' => true), 'Country')
        ->addColumn('city',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array('nullable' => true), 'City')
        ->addColumn('address',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array('nullable' => true), 'Address')
        ->addColumn('zip_code',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            10, array('nullable' => true), 'Zip code')
        ->addColumn('phone',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            12, array('nullable' => true), 'Landline phone number')
        ->addColumn('cell_phone',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            12, array('nullable' => true), 'Mobile phone number')
        ->addColumn('notes',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array('nullable' => true),
            'Notes')
        ->addForeignKey($installer->getFkName($table, 'subscriber_id', 'newsletter/subscriber', 'subscriber_id'),
            'subscriber_id', $installer->getTable('newsletter/subscriber'), 'subscriber_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment("ContactLab Id -> Newsletter subscriber mapping table");

    $connection->createTable($newTable);
}

$installer->endSetup();