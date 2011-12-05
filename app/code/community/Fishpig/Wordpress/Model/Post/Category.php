<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Post_Category extends Fishpig_Wordpress_Model_Category_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post_category');
	}
	
	/**
	 * Sets the category type as this class extends from a base class
	 * that can also be used for link categories
	 *
	 * @var string
	 */
	protected $_categoryType = 'category';

	/**
	 * Cache for this category's post collection
	 *
	 * @var Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected $_postCollection = null;
	
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

	public function loadBySlug($slug)
	{
		$collection = Mage::getResourceModel('wordpress/post_category_collection')
			->addFieldToFilter('slug', $slug)
			->setPageSize(1)->setCurPage(1);
		
		if (count($collection) > 0) {
			return $collection->getFirstItem();
		}
		
		return false;
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
    			->addCategoryIdFilter($this->getId());
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
		if (Mage::helper('wordpress')->isPluginEnabled('No Category Base')) {
			return Mage::helper('wordpress')->getUrl($this->getSlug());
		} else {
			return Mage::helper('wordpress')->getUrl(trim(Mage::helper('wordpress')->getCachedWpOption('category_base', 'category'), '/')	 . '/' . $this->getSlug());
		}
	}
}
