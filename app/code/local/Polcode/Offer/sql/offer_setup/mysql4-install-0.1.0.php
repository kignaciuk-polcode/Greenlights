<?php

$installer = $this;

$installer->startSetup();

//$installer->run("
//     
//    DROP TABLE IF EXISTS {$this->getTable('offer_inquiry')};
//    CREATE TABLE {$this->getTable('offer_inquiry')} (
//      `inquiry_id` int(11) unsigned NOT NULL auto_increment,
//      `customer_id` int(11) unsigned NOT NULL,        
//      `status` smallint(6) NOT NULL default '0',
//      `created_time` datetime NULL,
//      `update_time` datetime NULL,
//      PRIMARY KEY (`inquiry_id`)
//    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//    
//    DROP TABLE IF EXISTS {$this->getTable('offer_inquiry_item')};
//    CREATE TABLE {$this->getTable('offer_inquiry_item')} (
//      `item_id` int(11) unsigned NOT NULL auto_increment,
//      `inquiry_id` int(11) unsigned NOT NULL,
//      `product_id` int(11) unsigned NOT NULL,
//      PRIMARY KEY (`item_id`)
//    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;    
//    
//     
//        ");

$installer->endSetup();