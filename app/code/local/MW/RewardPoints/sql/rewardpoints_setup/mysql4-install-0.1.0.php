<?php

$installer = $this;
$collection = Mage::getModel('rewardpoints/customer')->getCollection();
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$collection->getTable('rewardpointshistory')};
CREATE TABLE {$collection->getTable('rewardpointshistory')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `type_of_transaction` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  `transaction_detail` varchar(255) NOT NULL default '',
  `transaction_time` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$collection->getTable('customer')};
CREATE TABLE {$collection->getTable('customer')} (
  `customer_id` int(11) unsigned NOT NULL auto_increment,
  `mw_reward_point` int(11) unsigned NOT NULL,
  `mw_friend_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 