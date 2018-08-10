<?php

$installer = $this;

$installer->startSetup();

$installer->run("
            ALTER TABLE `{$installer->getTable('superbrecommend/ordersQueue')}`
            ADD `updated_at` VARCHAR(50);
        ");

$installer->endSetup();