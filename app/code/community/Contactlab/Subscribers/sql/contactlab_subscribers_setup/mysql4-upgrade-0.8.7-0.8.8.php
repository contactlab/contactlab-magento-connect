<?php


$installer = $this;

$subscribersTable = $installer->getTable('newsletter/subscriber');
$conn = $installer->getConnection();

$installer->run(<<<EOT
alter table $subscribersTable add last_updated_at datetime default null;
EOT
);

$installer->run("update $subscribersTable set last_updated_at = utc_timestamp()");
