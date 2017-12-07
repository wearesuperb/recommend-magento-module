<?php

$installer = $this;

$installer->startSetup();

$installer->run("
            DROP TABLE IF EXISTS `{$installer->getTable('superbrecommend/ordersQueue')}`;
            CREATE TABLE `{$installer->getTable('superbrecommend/ordersQueue')}` (
              `id` INT(11) unsigned NOT NULL auto_increment,
              `email` VARCHAR(50),
              `order_id` VARCHAR(50),
              `cid` VARCHAR(50),
              `status` VARCHAR(50),
              `store_id` VARCHAR(50),
              `bid` VARCHAR(50),
              `customer_name` VARCHAR(50),
              `grand_total` VARCHAR(50),
              `tax` VARCHAR(50),
              `delivery` VARCHAR(50),
              `sale_qty` VARCHAR(50),
              `currency` VARCHAR(50),
              `products` TEXT,
              `segment` VARCHAR(50),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

$installer->endSetup();