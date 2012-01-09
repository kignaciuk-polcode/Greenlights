<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Feed_Category extends Fishpig_Wordpress_Block_Feed_Abstract
{
	/**
	 * Retrieve a collection of posts for the feed
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	public function getPosts()
	{
		$collection = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter()
			->setOrderByPostDate()
			->setPageSize(Mage::helper('wordpress')->getCachedWpOption('posts_per_rss', 10));
		
		if ($this->getCategory()) {
			$collection->addCategoryIdFilter($this->getCategory()->getId());
		}
		else {
			$collection->getSelect()->where('1=2');
		}
		
		return $collection;
	}
	
	/**
	 * Retrieve the feed URL
	 *
	 * @return string
	 */
	public function getFeedUrl()
	{
		if ($this->getCategory()) {
			return rtrim($this->getCategory()->getUrl(), '/') . '/feed/';
		}
		
		return '';
	}
	
	/**
	 * Determine whether to display the excerpt or full content
	 *
	 * @return bool
	 */
	public function displayExceprt()
	{
		return Mage::helper('wordpress')->getCachedWpOption('rss_use_excerpt');
	}
}
