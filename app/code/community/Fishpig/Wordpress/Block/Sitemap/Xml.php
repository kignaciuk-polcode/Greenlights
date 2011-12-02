<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sitemap_Xml extends Mage_Core_Block_Template
{
	public function getDocType()
	{
		return '<?xml version="1.0" encoding="'.$this->getBlogCharset().'"?>'."\n";
	}

	public function getBlogCharset()
	{
		return Mage::helper('wordpress')->getCachedWpOption('blog_charset');
	}
	
	public function getPosts()
	{
		$collection = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter()
			->setOrderByPostDate();

		return $collection;
	}
	
	public function getModifiedDate(Fishpig_Wordpress_Model_Post $post)
	{
		return substr($post->getPostModified(), 0, 10);
	}

	public function getPostPriority()
	{
		return floatval(Mage::getStoreConfig('wordpress_blog/xml_sitemap/post_priority'));
	}
	
	public function getPostChangeFrequency()
	{
		return Mage::getStoreConfig('wordpress_blog/xml_sitemap/post_change_frequency');
	}
	
	public function getHomepageChangeFrequency()
	{
		return Mage::getStoreConfig('wordpress_blog/xml_sitemap/homepage_change_frequency');	
	}
}
