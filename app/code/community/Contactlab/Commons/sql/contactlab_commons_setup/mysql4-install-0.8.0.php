<?php

$installer = $this;
$installer->startSetup();

$installer->run("drop table if exists {$installer->getTable("contactlab_commons/task")};");

$installer->run(<<<EOT
DROP TABLE IF EXISTS `contactlab_commons_task_entity`;
EOT
);

$installer->run(<<<EOT
CREATE TABLE `contactlab_commons_task_entity` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Task Id',
  `task_code` varchar(255) NOT NULL COMMENT 'Task Code',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `planned_at` timestamp NULL DEFAULT NULL COMMENT 'Task planned at date time (can be null!)',
  `description` varchar(255) NOT NULL COMMENT 'Task description',
  `task_data` text COMMENT 'Task internal data',
  `number_of_retries` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Number of retries',
  `max_retries` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Max number of retries',
  `retries_interval` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Minutes between retries',
  `model_name` varchar(64) NOT NULL COMMENT 'Magento model name',
  `status` varchar(20) NOT NULL DEFAULT 'new' COMMENT 'Task status',
  `max_value` bigint(20) DEFAULT NULL COMMENT 'Task progress max value',
  `progress_value` bigint(20) DEFAULT NULL COMMENT 'Task progress value',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store id',
  PRIMARY KEY (`task_id`),
  KEY `IDX_CONTACTLAB_COMMONS_TASK_ENTITY_STATUS` (`status`),
  KEY `FK_CONTACTLAB_COMMONS_TASK_ENTITY_STORE_ID_CORE_STORE_STORE_ID` (`store_id`),
  CONSTRAINT `FK_CONTACTLAB_COMMONS_TASK_ENTITY_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) COMMENT='Tasks';
EOT
);


$installer->run(<<<EOT
DROP TABLE IF EXISTS `contactlab_commons_task_event_entity`;
EOT
);

$installer->run(<<<EOT
CREATE TABLE `contactlab_commons_task_event_entity` (
  `task_event_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Task Event Id',
  `task_id` int(10) unsigned NOT NULL COMMENT 'Task Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `user_id` mediumint(9) unsigned DEFAULT NULL COMMENT 'User id',
  `description` varchar(255) NOT NULL COMMENT 'Task event description',
  `task_status` varchar(20) NOT NULL COMMENT 'Task status',
  `send_alert` tinyint(1) DEFAULT '0' COMMENT 'Show as alert',
  `email_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Email alert sent',
  PRIMARY KEY (`task_event_id`),
  KEY `FK_C3903EA306F9FB462C57D0501D1B13E8` (`task_id`),
  KEY `FK_CONTACTLAB_COMMONS_TASK_EVENT_ENTT_USR_ID_ADM_USR_USR_ID` (`user_id`),
  CONSTRAINT `FK_C3903EA306F9FB462C57D0501D1B13E8` FOREIGN KEY (`task_id`) REFERENCES `contactlab_commons_task_entity` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CONTACTLAB_COMMONS_TASK_EVENT_ENTT_USR_ID_ADM_USR_USR_ID` FOREIGN KEY (`user_id`) REFERENCES `admin_user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) COMMENT='Task events';
EOT
);


$installer->run(<<<EOT
DROP TABLE IF EXISTS `contactlab_commons_deleted_entity`;
EOT
);
$installer->run(<<<EOT
CREATE TABLE `contactlab_commons_deleted_entity` (
  `deleted_entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Deleted Entity Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `model` varchar(255) NOT NULL COMMENT 'Model',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `task_id` int(10) unsigned DEFAULT NULL COMMENT 'Task Id',
  `is_customer` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Is customer',
  `entity_id` int(10) unsigned DEFAULT NULL COMMENT 'Entity id',
  PRIMARY KEY (`deleted_entity_id`),
  KEY `FK_AB8E6FEC8986ED3EF2FA1B7DADE8FF28` (`task_id`),
  CONSTRAINT `FK_AB8E6FEC8986ED3EF2FA1B7DADE8FF28` FOREIGN KEY (`task_id`) REFERENCES `contactlab_commons_task_entity` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE
) COMMENT='Deleted customers and subscribers email log';
EOT
);



$installer->run(<<<EOT
DROP TABLE IF EXISTS `contactlab_commons_log_entity`;
EOT
);
$installer->run(<<<EOT
CREATE TABLE `contactlab_commons_log_entity` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Task Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `log_level` smallint(6) NOT NULL COMMENT 'Log level',
  `description` varchar(255) NOT NULL COMMENT 'Task description',
  PRIMARY KEY (`log_id`)
) COMMENT='Logs';
EOT
);



$installer->endSetup();
