<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sitemap_Xml extends Mage_Core_Block_Template
{
	/*
	 * Retrieve the feed doctype
	 *
	 * @return string
	 */
	public function getDocType()
	{
		return '';
		return '<?xml version="1.0" encoding="'.$this->getBlogCharset().'"?>'."\n";
	}
	
	/**
	 * Retrieve the charset used by the blog
	 *
	 * @return string
	 */
	public function getBlogCharset()
	{
		return Mage::helper('wordpress')->getCachedWpOption('blog_charset');
	}
	
	/**
	 * Retrieve a collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	public function getPosts()
	{
		$collection = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter()
			->setOrderByPostDate();

		return $collection;
	}
	
	/**
	 * Retrieve the date the post was modified
	 *
	 * @return string
	 */
	public function getModifiedDate(Fishpig_Wordpress_Model_Post $post)
	{
		return substr($post->getPostModified(), 0, 10);
	}

	/**
	 * Retrieve the post priority
	 *
	 * @return int
	 */
	public function getPostPriority()
	{
		return floatval(Mage::getStoreConfig('wordpress_blog/xml_sitemap/post_priority'));
	}

	/**
	 * Retrieve the post page change frequency
	 *
	 * @return float
	 */	
	public function getPostChangeFrequency()
	{
		return Mage::getStoreConfig('wordpress_blog/xml_sitemap/post_change_frequency');
	}
	
	/**
	 * Retrieve the homepage change frequency
	 *
	 * @return float
	 */
	public function getHomepageChangeFrequency()
	{
		return Mage::getStoreConfig('wordpress_blog/xml_sitemap/homepage_change_frequency');	
	}
}
