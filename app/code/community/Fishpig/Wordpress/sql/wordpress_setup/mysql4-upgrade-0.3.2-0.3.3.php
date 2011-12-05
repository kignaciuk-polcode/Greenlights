<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$this->run("
	
		CREATE TABLE IF NOT EXISTS {$this->getTable('wordpress_product_post')} (
		  `product_id` int(11) unsigned NOT NULL default 0,
		  `post_id` int(11) unsigned NOT NULL default 0,
		  PRIMARY KEY (`product_id`,`post_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		CREATE TABLE IF NOT EXISTS {$this->getTable('wordpress_product_category')} (
		  `product_id` int(11) unsigned NOT NULL default 0,
		  `category_id` int(11) unsigned NOT NULL default 0,
		  PRIMARY KEY (`product_id`,`category_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
	");

	$this->endSetup();
