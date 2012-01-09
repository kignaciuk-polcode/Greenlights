<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$tables = array(
		'wordpress_product_post',
		'wordpress_product_category',
		'wordpress_category_post',
		'wordpress_category_category',
	);

	$storeId = 1;
	
	if ($store = Mage::helper('wordpress')->getCurrentFrontendStore()) {
		if ($store->getId()) {
			$storeId = $store->getId();
		}
	}
	
	foreach($tables as $table) {
		$this->getConnection()->addColumn($this->getTable($table), 'store_id', " smallint(5) unsigned NOT NULL default 0");
		$this->getConnection()->update($this->getTable($table), array('store_id' => $storeId), '');
		$this->getConnection()->query("
			ALTER TABLE {$this->getTable($table)} 
			ADD FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		");
	}

	$this->endSetup();
	