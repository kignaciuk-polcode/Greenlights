<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$this->run("

		CREATE TABLE IF NOT EXISTS {$this->getTable('wordpress_autologin')} (
		  `autologin_id` int(11) unsigned NOT NULL auto_increment,
		  `username` varchar(40) NOT NULL default '',
		  `password` varchar(40) NOT NULL default '',
		  `user_id` int(9) unsigned NOT NULL default 0,
		  `store_id` int(9) unsigned NOT NULL default 0,
		  PRIMARY KEY (`autologin_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		ALTER TABLE {$this->getTable('wordpress_autologin')} ADD UNIQUE ( `user_id`);

	");
	
	$this->endSetup();
