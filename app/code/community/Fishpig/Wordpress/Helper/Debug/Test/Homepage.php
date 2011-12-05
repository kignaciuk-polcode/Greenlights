<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_Homepage extends Fishpig_Wordpress_Helper_Debug_Test_Abstract
{
	/**
	  * Test title
	  *
	  * @var string
	  */
	protected $_title = 'Blog As Homepage';
	
	/**
	 * Perform the test logic
	 * Checks whether the homepage is set up correctly
	 *
	 * @return bool
	 */
	protected function _performTest()
	{
		$blogAsHome = Mage::getStoreConfigFlag('wordpress_blog/layout/blog_as_homepage');
		$front = Mage::getStoreConfig('web/default/front');
		
		if ($blogAsHome) {
			if ($front != 'wordpress/homepage/index') {
				throw new Exception("You have set your blog as your Mageto homepage. To complete this, you need to change Web > Default Pages > Default Web URL to 'wordpress/homepage/index'.");
			}
			
			return true;
		}
		else if ($front == 'wordpress/homepage/index') {
			throw new Exception("You no longer want your blog as your Magento homepage. You need to reset the value at Web > Default Pages > Default Web URL to 'cms'.");
		}
		else if (!$front) {
			throw new Exception("The value at Web > Default Pages > Default Web URL is empty. This can lead to no homepage displaying in Magento. To display the standard Magento homepage, set this value to 'cms'");
		}
		
		$this->_canDisplay = false;
		
		return true;
	}
}
