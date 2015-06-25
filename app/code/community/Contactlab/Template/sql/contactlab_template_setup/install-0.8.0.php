<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$tableName = $installer->getTable("contactlab_template/type");

$installer->run("drop table if exists $tableName;");
$table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Template Type Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false
        ), 'Template type name')
        ->addColumn('template_type_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
            'nullable'  => false
            ), 'Code of template type')
        ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false
        ), 'Is a system defined type')
        ->addColumn('is_cron_enabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false
        ), 'Enable for cron execution (default for new templates)')
        ->setComment("Template types");

$installer->getConnection()->createTable($table);

$templateTable = $installer->getTable('newsletter/template');

$connection
    ->addColumn($templateTable,
		'enable_xml_delivery', array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => '1',
            'comment' => 'Use XML Delivery?'));
$connection
    ->addColumn($templateTable,
		'template_type_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'Template type id'));
$connection
    ->addColumn($templateTable,
		'flg_html_txt', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'Text, html or both',
            'default' => 'B',
            'length' => 1));
$connection
    ->addColumn($templateTable,
        'template_text_plain', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'length' => '64k',
            'comment' => 'Template Text (txt)'));

$connection
    ->addForeignKey(
        $installer->getFkName('newsletter/template', 'template_type_id', 'contactlab_template/type', 'entity_id'),
        $templateTable,
        'template_type_id',
        $installer->getTable('contactlab_template/type'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT, Varien_Db_Ddl_Table::ACTION_RESTRICT);

foreach (range(1, 5) as $i) {
    $connection
        ->addColumn($templateTable,
            "template_pr_txt_$i", array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'length' => '64k',
                'comment' => 'Template Product Text (txt)'));
    $connection
        ->addColumn($templateTable,
            "template_pr_html_$i", array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'length' => '64k',
                'comment' => 'Template Product Text (html)'));
}

$connection
    ->addColumn($templateTable,
		'default_product_snippet', array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => true,
            'comment' => 'Default product snippet number'));

// Cron fields
$connection
    ->addColumn($templateTable,
		'is_cron_enabled', array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'comment' => 'Does the template is active for cron?'));
$connection
    ->addColumn($templateTable,
		'cron_date_range_start', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'comment' => 'Start date for cron'));
$connection
    ->addColumn($templateTable,
		'cron_date_range_end', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'comment' => 'End date for cron'));
$connection
    ->addColumn($templateTable,
		'queue_delay_time', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Optional queue delay time'));



$installer->endSetup();
