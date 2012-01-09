<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$this->run("
	
		CREATE TABLE IF NOT EXISTS {$this->getTable('wordpress_category_post')} (
		  `category_id` int(11) unsigned NOT NULL default 0,
		  `post_id` int(11) unsigned NOT NULL default 0,
		  `position` int(4) unsigned NOT NULL default 0,
		  PRIMARY KEY (`category_id`,`post_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	");

	$this->endSetup();

