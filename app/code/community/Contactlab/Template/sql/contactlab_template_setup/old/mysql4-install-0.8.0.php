<?php

$installer = $this;
$installer->startSetup();



$installer->run(<<<EOT

DROP TABLE IF EXISTS `contactlab_template_type_entity`;
EOT
);

$installer->run(<<<EOT
CREATE TABLE `contactlab_template_type_entity` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Template type Id',
  `name` varchar(255) NOT NULL COMMENT 'Template type name',
  `template_type_code` varchar(32) NOT NULL COMMENT 'Code of template type',
  `is_system` smallint(6) NOT NULL COMMENT 'Is a system defined type',
  `is_cron_enabled` smallint(6) NOT NULL COMMENT 'Enable for cron execution (default for new templates)',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Template types';
EOT
);

$installer->endSetup();
