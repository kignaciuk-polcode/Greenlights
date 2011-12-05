<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_Connection extends Fishpig_Wordpress_Helper_Debug_Test_Abstract
{
	/**
	  * Test title
	  *
	  * @var string
	  */
	protected $_title = 'Database Connection';
	
	/**
	 * Perform the test logic
	 * Checks whether database is connected
	 *
	 * @return bool
	 */
	protected function _performTest()
	{
		return Mage::helper('wordpress/db')->isConnected(false);
	}
}
