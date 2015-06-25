<?php

$installer = $this;
$installer->startSetup();

$installer->run(<<<EOT
DROP TABLE IF EXISTS `contactlab_subscribers_stats`;
EOT
);

$installer->run(<<<EOT
CREATE TABLE `contactlab_subscribers_stats` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity id',
  `customer_id` int(10) unsigned NOT NULL COMMENT 'Customer id',
  `last_order_date` datetime DEFAULT NULL COMMENT 'Last order date',
  `last_order_amount` float DEFAULT NULL COMMENT 'Last order amount',
  `last_order_products` int(11) DEFAULT NULL COMMENT 'Nr of products in last order',
  `total_orders_amount` float DEFAULT NULL COMMENT 'Total orders amount',
  `total_orders_products` int(11) DEFAULT NULL COMMENT 'Total nr of products',
  `total_orders_count` int(11) DEFAULT NULL COMMENT 'Total nr of orders',
  `avg_orders_amount` float DEFAULT NULL COMMENT 'Avg order amount',
  `avg_orders_products` float DEFAULT NULL COMMENT 'Avg number of products',
  `period1_amount` float DEFAULT NULL COMMENT 'Order amount in period 2',
  `period1_products` int(11) DEFAULT NULL COMMENT 'Nr of products in period 1',
  `period1_orders` int(11) DEFAULT NULL COMMENT 'Nr of orders in period 1',
  `period2_amount` float DEFAULT NULL COMMENT 'Order amount in period 2',
  `period2_products` int(11) DEFAULT NULL COMMENT 'Nr of products in period 2',
  `period2_orders` int(11) DEFAULT NULL COMMENT 'Nr of orders in period 2',
  PRIMARY KEY (`entity_id`),
  KEY `FK_CONTACTLAB_SUBSCRIBERS_STATS_CSTR_ID_CSTR_ENTT_ENTT_ID` (`customer_id`),
  CONSTRAINT `FK_CONTACTLAB_SUBSCRIBERS_STATS_CSTR_ID_CSTR_ENTT_ENTT_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) COMMENT='Subscribers stats';
EOT
);


$installer->run(<<<EOT
DROP TABLE IF EXISTS `contactlab_subscribers_uk`;
EOT
);

$installer->run(<<<EOT
CREATE TABLE `contactlab_subscribers_uk` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ContactLab Entity id',
  `subscriber_id` int(10) unsigned DEFAULT NULL COMMENT 'Subscriber Id',
  `customer_id` int(10) unsigned DEFAULT NULL COMMENT 'Customer Id',
  `is_exported` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is exported',
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`),
  UNIQUE KEY `subscriber_id_2` (`subscriber_id`),
  KEY `IDX_CONTACTLAB_SUBSCRIBERS_UK_SUBSCRIBER_ID` (`subscriber_id`),
  KEY `IDX_CONTACTLAB_SUBSCRIBERS_UK_CUSTOMER_ID` (`customer_id`),
  CONSTRAINT `FK_2D221C36F67F3B45106183533744E1D2` FOREIGN KEY (`subscriber_id`) REFERENCES `newsletter_subscriber` (`subscriber_id`) ON DELETE SET NULL,
  CONSTRAINT `FK_CONTACTLAB_SUBSCRIBERS_UK_CSTR_ID_CSTR_ENTT_ENTT_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16493 DEFAULT CHARSET=utf8 COMMENT='Real ContactLab Id Table';
EOT
);
