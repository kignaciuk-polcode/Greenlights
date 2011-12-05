<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_WpUrls extends Fishpig_Wordpress_Helper_Debug_Test_Abstract
{
	/**
	  * Test title
	  *
	  * @var string
	  */
	protected $_title = 'WordPress URL\'s';
	
	const _error_matching_urls = 'Your blog URL (site address) matches your install URL (WordPress address). Please change your blog route or move WordPress to a different sub-directory';
	
	/**
	 * Perform the test logic
	 * Checks whether the WordPress install and blog URL match
	 *
	 * @return bool
	 */
	protected function _performTest()
	{
		if (Mage::helper('wordpress/db')->isConnected() && Mage::helper('wordpress/db')->isQueryable()) {
			if (!$this->isFullyIntegrated()) {
				return false;
			}
			
			return $this->wpUrlsMatch(false);
		}
		
		throw new Exception(self::_error_cannot_test);
	}

	/**
	 * Returns true if the install that WordPress is installed on (WP-Option: home) is the 
	 * same as the site URL (WP-Option: siteurl)
	 *
	 * @param bool $graceful
	 * @return bool
	 */
	public function wpUrlsMatch($graceful = true)
	{
		$blogUrl = rtrim($this->removeIndexFromUrl(Mage::helper('wordpress')->getUrl()), '/');
		$installUrl = rtrim($this->getCachedWpOption('siteurl'), '/');

		if ($blogUrl == $installUrl) {
			if (!$graceful) {
				throw new Exception(self::_error_matching_urls);
			}
			
			return false;
		}
	
		return true;	
	}
	
}
