<?php

$installer = $this;

$installer->startSetup();

$installer->run("
     
    DROP TABLE IF EXISTS {$this->getTable('offer_inquiry')};
    CREATE TABLE {$this->getTable('offer_inquiry')} (
  `inquiry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL,
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  `sharing_code` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`inquiry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('offer_inquiry_item')};
    CREATE TABLE {$this->getTable('offer_inquiry_item')} (
  `inquiry_item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` int(11) unsigned NOT NULL,
  `store_id` int(10) NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_qty` int(10) unsigned NOT NULL,
  `custom_price` decimal(12,4) DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`inquiry_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS {$this->getTable('offer_inquiry_item_option')};
    CREATE TABLE {$this->getTable('offer_inquiry_item_option')} (    
   `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Option Id',
  `inquiry_item_id` int(11) unsigned NOT NULL COMMENT 'Inquiry Item Id',
  `product_id` int(10) unsigned NOT NULL COMMENT 'Product Id',
  `code` varchar(255) NOT NULL COMMENT 'Code',
  `value` text COMMENT 'Value',
  PRIMARY KEY (`option_id`),
  KEY `inquiry_item_id` (`inquiry_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Inquiry Item Option Table';   
     
        ");

$installer->endSetup();