<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Homepage extends Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
{
	/**
	 * Get's the blog title
	 *
	 * @return string
	 */
	public function getBlogTitle()
	{
		return Mage::helper('wordpress')->getCachedWpOption('blogname');
	}
	
	/**
	 * Returns the blog homepage URL
	 *
	 * @return string
	 */
	public function getBlogHomepageUrl()
	{
		return Mage::helper('wordpress')->getUrl();
	}
}
