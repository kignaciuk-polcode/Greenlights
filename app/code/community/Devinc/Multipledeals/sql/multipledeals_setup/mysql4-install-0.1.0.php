<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('multipledeals')};
CREATE TABLE {$this->getTable('multipledeals')} (
  `multipledeals_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `deal_price` decimal(12,2) NOT NULL,
  `deal_qty` int(11) NOT NULL,
  `date_from` date NULL,
  `date_to` date NULL,
  `time_from` varchar(255) NOT NULL default '',
  `time_to` varchar(255) NOT NULL default '',
  `qty_sold` int(11) NOT NULL,
  `nr_views` int(11) NOT NULL,
  `disable` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`multipledeals_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
	
$installer->setConfigData('multipledeals/configuration/enabled',					1);
$installer->setConfigData('multipledeals/configuration/header_links',				1);
$installer->setConfigData('multipledeals/configuration/qty',	    				1);
$installer->setConfigData('multipledeals/configuration/past_deals',	    			1);
$installer->setConfigData('multipledeals/configuration/no_deal_message',	    	'There are no deals currently setup. Please try again later.');
$installer->setConfigData('multipledeals/configuration/notify',	    				0);
$installer->setConfigData('multipledeals/configuration/admin_email',	    		'');

$installer->setConfigData('multipledeals/sidebar_configuration/sidedeals_number',	3);
$installer->setConfigData('multipledeals/sidebar_configuration/left_sidebar',		0);
$installer->setConfigData('multipledeals/sidebar_configuration/right_sidebar',		1);
$installer->setConfigData('multipledeals/sidebar_configuration/display_price',		1);
$installer->setConfigData('multipledeals/sidebar_configuration/display_qty',		1);

$installer->setConfigData('multipledeals/countdown_configuration/display_days',	    0);
$installer->setConfigData('multipledeals/countdown_configuration/bg_main',	   		'#FFFFFF');
$installer->setConfigData('multipledeals/countdown_configuration/bg_color',	   		'#333333');
$installer->setConfigData('multipledeals/countdown_configuration/alpha',	   		'70');
$installer->setConfigData('multipledeals/countdown_configuration/textcolor',	   	'#FFFFFF');
$installer->setConfigData('multipledeals/countdown_configuration/txt_color',	   	'#333333');
$installer->setConfigData('multipledeals/countdown_configuration/sec_text',	   		'SECONDS');
$installer->setConfigData('multipledeals/countdown_configuration/min_text',	   		'MINUTES');
$installer->setConfigData('multipledeals/countdown_configuration/hour_text',	   	'HOURS');
$installer->setConfigData('multipledeals/countdown_configuration/days_text',	   	'DAYS');

$installer->endSetup(); 