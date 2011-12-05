<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Category_View extends Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
{
	/**
	 * Returns the current Wordpress category
	 * This is just a wrapper for getCurrentCategory()
	 *
	 * @return Fishpig_Wordpress_Model_Post_Categpry
	 */
	public function getCategory()
	{
		return $this->getCurrentCategory();
	}
	
	/**
	 * Caches and returns the current category
	 *
	 * @return Fishpig_Wordpress_Model_Post_Categpry
	 */
	public function getCurrentCategory()
	{
		if (!$this->hasWordpressCategory()) {
			if ($categoryId = $this->getCategoryId()) {
				$category = Mage::getModel('wordpress/post_category')->load($categoryId);
				
				if ($category->getId() == $categoryId) {
					$this->setCategory($category);
				}
			}
			else {
				$this->setCategory(Mage::registry('wordpress_category'));
			}
		}
		
		return $this->getData('category');
	}

	/**
	 * Retrieve the current category ID
	 *
	 * @return false|int
	 */
	public function getCurrentCategoryId()
	{
		if ($category = $this->getCurrentCategory()) {
			return $category->getId();
		}
		
		return false;
	}
	
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		if (is_null($this->_postCollection)) {
			$this->_postCollection = parent::_getPostCollection()
				->addCategoryIdFilter($this->getCurrentCategoryId());
		}
		
		return $this->_postCollection;
	}
}
