<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Category_Abstract extends Mage_Core_Model_Abstract
{
	abstract public function getPostCollection();
	
	/**
	 * Defines what type of category this is
	 *
	 * @var string (category / link_category)
	 */
	protected $_categoryType = '';
	
	public function getId()
	{
		return $this->getData('term_id');
	}
	
	/**
	 * Get this category's parent category
	 *
	 * @return Fishpig_Wordpress_Model_Category_Abstract
	 */
	public function getParentCategory()
	{
		return Mage::getModel($this->getResourceName())->load($this->getParentId());
	}
	/**
	 * Retrieve the category type
	 * Needed because several entites extend from this (category, term etc)
	 *
	 * @return string
	 */
	public function getCategoryType()
	{
		return $this->_categoryType;
	}
    
    /**
     * Get the Category URL
     *
     * @return string
     */
    public function getUrl()
    {
		return rtrim(Mage::helper('wordpress')->getUrl('category/' . $this->getSlug()), '/') . '/';
    }

	/**
	 * Returns the amount of posts related to this object
	 *
	 * @return int
	 */
    public function getPostCount()
    {
    	return count($this->getPostCollection());
    }

	/**
	 * Retrieve a collection of children categories
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Category_Collection_Abstract
	 */
	public function getChildrenCategories()
	{
		if (!$this->getData('children_categories')) {
			$children = $this->getCollection()->addParentIdFilter($this->getId());
			
			$this->setChildrenCategories($children);
		}
	
		return $this->getData('children_categories');
	}
}
