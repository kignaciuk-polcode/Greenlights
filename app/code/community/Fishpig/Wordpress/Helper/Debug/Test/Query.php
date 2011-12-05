<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_Query extends Fishpig_Wordpress_Helper_Debug_Test_Abstract
{
	/**
	  * Test title
	  *
	  * @var string
	  */
	protected $_title = 'Database Query';
	
	/**
	 * Perform the test logic
	 * Checks whether database is connected
	 *
	 * @return bool
	 */
	protected function _performTest()
	{
		if (Mage::helper('wordpress/db')->isConnected()) {
			return Mage::helper('wordpress/db')->isQueryable(false);
		}
		
		throw new Exception(self::_error_cannot_test);
	}
}
