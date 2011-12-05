<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Mysql4_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * This ensures that the WordPress database connection has been initialised
	 * This fixes issues when calling WordPress functions via the CRON
	 * As the necessary event is not triggered
	 *
	 */
	public function __construct()
	{
		if (!Mage::helper('wordpress/db')->hasBeenInitialised()) {
			Mage::getSingleton('wordpress/observer_databaseSetup')->initConnection(null);
		}
		
		parent::__construct();
	}
	
	/**
	 * Retrieve the appropriate read adapter
	 */
	protected function _getReadAdapter()
	{
		if (Mage::helper('wordpress/db')->isSameDatabase()) {
			return Mage::getSingleton('core/resource')->getConnection('core_read');
		}
		
		return $this->_getWordPressAdapter();
	}

	/**
	 * Retrieve the appropriate write adapter
	 */	
	protected function _getWriteAdapter()
	{
		if (Mage::helper('wordpress/db')->isSameDatabase()) {
			return Mage::getSingleton('core/resource')->getConnection('core_write');
		}
		
		return $this->_getWordPressAdapter();
	}
	
	/**
	 * Retrieve the WordPress database adapter
	 *
	 */
	protected function _getWordPressAdapter()
	{
		return Mage::getSingleton('core/resource')->getConnection('wordpress');
	}
}
