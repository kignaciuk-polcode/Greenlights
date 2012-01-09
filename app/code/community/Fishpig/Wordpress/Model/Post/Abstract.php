<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Post_Abstract extends Mage_Core_Model_Abstract
{
	/**
	 * Load a page by a URI slug (post_name)
	 * This is useful for loading pages based on the URL
	 *
	 * @param string slug
	 * @return Fishpig_Wordpress_Model_Post_Abstract
	 */
	public function loadBySlug($slug)
	{
		return $this->load($slug, 'post_name');
	}
	
	/**
	 * Retrieve the URL for the comments feed
	 *
	 * @return string
	 */
	public function getCommentFeedUrl()
	{
		return rtrim($this->getPermalink(), '/') . '/feed/';
	}
	 
	/**
	 * Gets the post content
	 * If parameter1 is true, nl2br is added
	 *
	 * @return string
	 */
	public function getPostContent()
	{
		if (!$this->hasFilteredPostContent()) {
			$this->setFilteredPostContent(Mage::helper('wordpress/filter')->applyFilters($this->getData('post_content'), array('object' => $this, 'type' => 'content')));
		}
		
		return $this->getData('filtered_post_content');
	}
	
	/**
	 * Returns a collection of comments for this post
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Comment_Collection
	 */
	public function getComments()
	{
		if (!$this->hasData('comments')) {
			$this->setData('comments', $this->getResource()->getPostComments($this));
		}
		
		return $this->getData('comments');
	}

	/**
	 * Returns a collection of images for this post
	 * 
	 * @return Fishpig_Wordpress_Model_Mysql4_Image_Collection
	 *
	 * NB. This function has not been thoroughly tested
	 *        Please report any bugs
	 */
	public function getImages()
	{
		if (!$this->hasData('images')) {
			$this->setImages(Mage::getResourceModel('wordpress/image_collection')->setParent($this->getData('ID')));
		}
		
		return $this->getData('images');
	}

	/**
	 * Returns the featured image for the post
	 *
	 * This image must be uploaded and assigned in the WP Admin
	 *
	 * @return Fishpig_Wordpress_Model_Image
	 */
	public function getFeaturedImage()
	{
		if (!$this->hasData('featured_image')) {
			$this->setFeaturedImage($this->getResource()->getFeaturedImage($this));
		}
	
		return $this->getData('featured_image');	
	}
	
	/**
	 * Get the model for the author of this post
	 *
	 * @return Fishpig_Wordpress_Model_Author
	 */
	public function getAuthor()
	{
		return Mage::getModel('wordpress/user')->load($this->getAuthorId());	
	}
	
	/**
	 * Returns the author ID of the current post
	 *
	 * @return int
	 */
	public function getAuthorId()
	{
		return $this->getData('post_author');
	}
	
	/**
	 * Returns the post date formatted
	 * If not format is supplied, the format specified in your Magento config will be used
	 *
	 * @return string
	 */
	public function getPostDate($format = null)
	{
		if ($this->getData('post_date_gmt') && $this->getData('post_date_gmt') != '0000-00-00 00:00:00') {
			return Mage::helper('wordpress')->formatDate($this->getData('post_date_gmt'), $format);
		}
	}
	
	/**
	 * Returns the post time formatted
	 * If not format is supplied, the format specified in your Magento config will be used
	 *
	 * @return string
	 */
	public function getPostTime($format = null)
	{
		if ($this->getData('post_date_gmt') && $this->getData('post_date_gmt') != '0000-00-00 00:00:00') {
			return Mage::helper('wordpress')->formatTime($this->getData('post_date_gmt'), $format);
		}
	}
	
	/*
	 * Submit a comment for this post
	 *
	 * @param string $name
	 * @param string $email
	 * @param string $url
	 * @param string $comment
	 * @return Fishpig_Wordpress_Model_Post_Comment
	 */
	public function postComment($name, $email, $url, $comment)
	{
		return $this->getResource()->postComment($this, $name, $email, $url, $comment);
	}
	
	/**
	 * Retrieve the META description for the post
	 * If All In One SEO is not installed, auto-generate from excerpt/content
	 *
	 * @return string
	 */
	public function getMetaDescription()
	{
		if (!$this->getData('meta_description')) {
			$this->setMetaDescription($this->getResource()->getMetaDescription($this));
		}
		
		return $this->getData('meta_description');
	}
	
	/**
	 * Retrieve the META description for the post
	 * If All In One SEO is not installed, auto-generate from excerpt/content
	 *
	 * @return string
	 */
	public function getMetaKeywords()
	{
		if (!$this->getData('meta_keywords')) {
			$this->setMetaKeywords($this->getResource()->getMetaKeywords($this));
		}
		
		return $this->getData('meta_keywords');
	}

	/**
	 * Retrieve the page title for the post
	 * If All In One SEO is not installed, auto-generate from post title
	 *
	 * @return string
	 */	
	public function getPageTitle()
	{
		if (!$this->getData('page_title')) {
			$this->setPageTitle($this->getResource()->getPageTitle($this));
		}
		
		return $this->getData('page_title');
	}
	
	/**
	 * Retrieve a custom field value from the database
	 *
	 * @param string $key
	 * @return string
	 */
	public function getCustomField($key)
	{
		if (!$this->hasData($key)) {
			$this->setData($key, $this->getResource()->getMetaValue($this, $key));
		}
		
		return $this->getData($key);	
	}
	
	/**
	 * Determine whether the post has been published
	 *
	 * @return bool
	 */
	public function isPublished()
	{
		return $this->getPostStatus() == 'publish';
	}

	/**
	 * Determine whether the post has been published
	 *
	 * @return bool
	 */
	public function isPending()
	{
		return $this->getPostStatus() == 'pending';
	}

	
	/**
	 * Retrieve the preview URL
	 *
	 * @return string
	 */
	public function getPreviewUrl()
	{
		if ($this->isPending()) {
			return Mage::helper('wordpress')->getUrl('?p=' . $this->getId() . '&preview=1');
		}
		
		return '';
	}
}
