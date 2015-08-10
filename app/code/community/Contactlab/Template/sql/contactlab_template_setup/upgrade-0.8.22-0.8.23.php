<?php

$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();

/* @var $table newsletter template table name */
$table = $installer->getTable('newsletter/template');

/** @var $columns array with column name => comment*/
$columns = array(
    'template_text' => 'Template Text',
    'template_text_preprocessed' => 'Template Text Preprocessed',
    'template_text_plain' => 'Template Text (txt)',
);

// Alter each column table
foreach ($columns as $columnName => $comment) {
    $connection->modifyColumn($table, $columnName, "mediumtext comment '$comment'");
}

$installer->endSetup();