<?php

$installer = $this;

$installer->startSetup();

$installer->run("
            DROP TABLE IF EXISTS `{$installer->getTable('superbrecommend/ordersQueue')}`;
            CREATE TABLE `{$installer->getTable('superbrecommend/ordersQueue')}` (
              `id` INT(11) unsigned NOT NULL auto_increment,
              `email` VARCHAR(255),
              `order_id` VARCHAR(255),
              `cid` VARCHAR(255),
              `status` VARCHAR(255),
              `store_id` VARCHAR(255),
              `segment` VARCHAR(255),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

$installer->endSetup();