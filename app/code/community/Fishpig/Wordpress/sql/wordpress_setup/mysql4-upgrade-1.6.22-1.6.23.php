<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('wordpress_product_post'), 'position', " int(4) unsigned NOT NULL default 0");
	$this->getConnection()->addColumn($this->getTable('wordpress_product_category'), 'position', " int(4) unsigned NOT NULL default 0");

	$this->endSetup();
