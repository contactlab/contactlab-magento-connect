<?php


$installer = $this;

$subscribersTable = $installer->getTable('newsletter/subscriber');
$conn = $installer->getConnection();

$installer->run(<<<EOT
alter table $subscribersTable add last_subscribed_at datetime default null;
EOT
);

