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
	 * Retrieve the appropriate read adapter
	 */
	protected function _getReadAdapter()
	{
		return Mage::helper('wordpress/db')->getReadAdapter();
	}

	/**
	 * Retrieve the appropriate write adapter
	 */	
	protected function _getWriteAdapter()
	{
		return Mage::helper('wordpress/db')->getWriteAdapter();
	}
}
