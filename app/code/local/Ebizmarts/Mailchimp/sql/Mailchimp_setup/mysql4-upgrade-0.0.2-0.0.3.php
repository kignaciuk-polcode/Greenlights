<?php

$installer = $this;

$installer->startSetup();

$installer->run("

  DROP TABLE IF EXISTS `{$this->getTable('ebizmarts_ecomm360')}`;
  CREATE TABLE `{$this->getTable('ebizmarts_ecomm360')}` (
	`ecomm_id` INT( 11 ) unsigned NOT NULL auto_increment ,
	`store_id` SMALLINT( 5 ) NOT NULL default '0',
	`increment_id` VARCHAR( 50 ) NOT NULL default '',
	`order_id` INT( 10 ) NOT NULL default '0',
	`campaign_id` VARCHAR( 128 ) NOT NULL default '',
	`sent_time` DATETIME NOT NULL ,
	PRIMARY KEY ( `ecomm_id` )
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();