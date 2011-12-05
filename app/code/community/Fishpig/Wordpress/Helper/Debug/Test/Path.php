<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_Path extends Fishpig_Wordpress_Helper_Debug_Test_Abstract
{
	/**
	  * Test title
	  *
	  * @var string
	  */
	protected $_title = 'WordPress Path';

	const _error_incorrect_path = 'Your WordPress path is incorrect. You must set the path to your WordPress installation';
	
	/**
	 * Perform the test logic
	 * Checks whether the WordPress install and blog URL match
	 *
	 * @return bool
	 */
	protected function _performTest()
	{
		$wpPath = Mage::helper('wordpress')->getWordPressPath();
		if ($wpPath && is_dir($wpPath) && is_file(rtrim($wpPath, '/\\') . DS . 'wp-config.php')) {
			return true;
		}

		throw new Exception(self::_error_incorrect_path);
	}
}
