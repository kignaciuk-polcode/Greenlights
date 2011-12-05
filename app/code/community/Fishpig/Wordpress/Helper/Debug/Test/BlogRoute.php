<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_BlogRoute extends Fishpig_Wordpress_Helper_Debug_Test_Abstract
{
	/**
	  * Test title
	  *
	  * @var string
	  */
	protected $_title = 'Blog Route';
	
	const _error_mismatched_urls = 'Go to the General Settings page of your WordPress Admin and set the \'Site address (URL)\' field to \'%s\'';
	
	const _error_empty_route = 'Your blog route is either empty or contains invalid characters.';

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
			
			if ($this->getBlogRoute()) {
				return $this->isBlogRouteCorrect(false);
			}
			
			throw new Exception(self::_error_empty_route);
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
	public function isBlogRouteCorrect($graceful = true)
	{
		$wpBlogUrl = rtrim($this->getCachedWpOption('home'), '/');
		$mageBlogUrl = rtrim(($this->getUrl()), '/');
		
		if ($wpBlogUrl != $mageBlogUrl) {
			if (!$graceful) {
				if (preg_match('/(.*\/)admin\/(' . $this->getBlogRoute() . '.*)/', $mageBlogUrl, $results)) {
					if ($store = $this->getDefaultStore()) {
						$mageBlogUrl = $results[1] . $store->getCode() . '/' . $results[2];
					}
					else {
						$mageBlogUrl = $results[1] . '[[store_code]]' . '/' . $results[2];
					}
				}

				throw new Exception(sprintf(self::_error_mismatched_urls, $mageBlogUrl));
			}
			
			return false;
		}
	
		return true;	
	}
}
