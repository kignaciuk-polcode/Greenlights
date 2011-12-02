<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar_Widget_Categories extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Returns the current category collection
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Category_Collection
	 */
	public function getCategories()
	{
		if (!$this->hasCategories()) {
			$collection = Mage::getResourceModel('wordpress/post_category_collection')
				->addParentIdFilter($this->getParentId());
			
			$collection->getSelect()->order('name ASC');

			$this->setCategories($collection);
		}
		
		return $this->getData('categories');
	}
	
	/**
	 * Returns the parent ID used to display categories
	 * If parent_id is not set, 0 will be returned and root categories displayed
	 *
	 * @return int
	 */
	public function getParentId()
	{
		return number_format($this->getData('parent_id'), 0, '', '');
	}
	
	public function isCurrentCategory($category)
	{
		if ($this->getCurrentCategory()) {
			return $category->getId() == $this->getCurrentCategory()->getId();
		}
		
		return false;
	}
	
	public function getCurrentCategory()
	{
		if (!$this->hasCurrentCategory()) {
			$this->setCurrentCategory(Mage::registry('wordpress_category'));
		}
		
		return $this->getData('current_category');
	}
}
