<?php

$installer = $this;

$sql = "
		ALTER TABLE `{$installer->getTable('sales/quote')}` 
		ADD `rewardpoint_discount` INT UNSIGNED NOT NULL,
		ADD `rewardpoint` INT UNSIGNED NOT NULL;
		";
$installer->run($sql);
$installer->endSetup();