<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$this->run("
		ALTER TABLE {$this->getTable('wordpress_autologin')} MODIFY username varchar(150);
		ALTER TABLE {$this->getTable('wordpress_autologin')} MODIFY password varchar(150);
	");

	$this->endSetup();

