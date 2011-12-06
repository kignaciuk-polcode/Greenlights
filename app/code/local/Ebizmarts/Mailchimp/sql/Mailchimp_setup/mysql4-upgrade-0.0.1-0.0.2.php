<?php

$installer = $this;

$installer->startSetup();

$installer->run("

  DROP TABLE IF EXISTS `{$this->getTable('ebizmarts_mailchimppro')}`;
  CREATE TABLE `{$this->getTable('ebizmarts_mailchimppro')}` (
	`mailchimppro_id` INT( 11 ) unsigned NOT NULL auto_increment ,
	`store_id` SMALLINT( 5 ) NOT NULL ,
	`customer_id` INT( 11 ) NOT NULL ,
	`current_email` VARCHAR( 128 ) NOT NULL ,
	`member_id` VARCHAR( 128 ) NOT NULL ,
	`is_subscribed` BOOLEAN NOT NULL ,
	`list_id`  VARCHAR( 12 ) NOT NULL ,
	`created_time` DATETIME NOT NULL ,
	`updated_time` DATETIME NOT NULL ,
	PRIMARY KEY ( `mailchimppro_id` )
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();