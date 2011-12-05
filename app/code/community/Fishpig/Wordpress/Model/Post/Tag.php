<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Post_Tag extends Fishpig_Wordpress_Model_Category_Abstract
{
	/**
	 * Sets the category type as this class extends from a base class
	 * that can also be used for link categories
	 *
	 * @var string
	 */
	protected $_categoryType = 'post_tag';

	/**
	 * Cache for this category's post collection
	 *
	 * @var Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected $_postCollection = null;

	public function _construct()
	{
		$this->_init('wordpress/post_tag');
	}
	
	/**
	 * Loads a category model based on a post ID
	 * 
	 * @param int $postId
	 */
	public function loadByPostId($postId)
	{
		$this->load($postId, 'object_id');
		return $this;
	}
	
	/**
	 * Loads the posts belonging to this category
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */    
    public function getPostCollection()
    {
    	if (is_null($this->_postCollection)) {
    		$this->_postCollection = Mage::getResourceModel('wordpress/post_collection')
    			->addIsPublishedFilter()
    			->addTagIdFilter($this->getId());
    	}
    	
    	return $this->_postCollection;
    }

	/**
	 * Gets the category URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return rtrim(Mage::helper('wordpress')->getUrl(trim(Mage::helper('wordpress')->getCachedWpOption('tag_base', 'tag'), '/')	 . '/' . $this->getSlug()), '/') . '/';
	}
	
	public function getAllTagsUrl()
	{
		return Mage::helper('wordpress')->getUrl('tags');
	}
}
