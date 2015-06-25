<?php

$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();

$tableName = $installer->getTable('contactlab_template/type');

$min = $connection->fetchOne("select min(entity_id) from $tableName where is_system = 1 and template_type_code = 'CART'");
if ($min) {
    $connection->query("delete from $tableName where is_system = 1 and template_type_code = 'CART' and entity_id > $min");
}

$min = $connection->fetchOne("select min(entity_id) from $tableName where is_system = 1 and template_type_code = 'WISHLIST'");
if ($min) {
    $connection->query("delete from $tableName where is_system = 1 and template_type_code = 'WISHLIST' and entity_id > $min");
}

$min = $connection->fetchOne("select min(entity_id) from $tableName where is_system = 1 and template_type_code = 'GENERIC'");
if ($min) {
    $connection->query("delete from $tableName where is_system = 1 and template_type_code = 'GENERIC' and entity_id > $min");
}


$installer->endSetup();
