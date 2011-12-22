<?php

$this->startSetup();

$this->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('giftcards_card')} (
  `card_id` int(10) unsigned NOT NULL auto_increment,
  `card_code` varchar(15) NOT NULL,
  `initial_value` decimal(12,4) NOT NULL,
  `current_balance` decimal(12,4) NOT NULL,
  `currency_code` char(3) NOT NULL,
  `order_link` varchar(40) NOT NULL,
  `date_created` datetime NOT NULL,
  `status` char(1) NOT NULL,
  `gift_card_type` char(1) NOT NULL,
  `mail_sender` varchar(64) NOT NULL,
  `mail_recipient` varchar(64) NOT NULL,
  `mail_address` varchar(64) NOT NULL,
  `mail_massege` text NOT NULL,
  `mail_day2send` date NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `is_mail_sent` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$this->endSetup();
