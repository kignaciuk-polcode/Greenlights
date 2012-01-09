<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post_Collection extends Fishpig_Wordpress_Model_Mysql4_Post_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post');
	}

	/**
	 * Filters the collection by an array of post ID's and category ID's
	 * When filtering by a category ID, all posts from that category will be returned
	 * If you change the param $operator to AND, only posts that are in a category specified in
	 * $categoryIds and $postIds will be returned
	 *
	 * @param mixed $postIds
	 * @param mixed $categoryIds
	 * @param string $operator
	 */
	public function addCategoryAndPostIdFilter($postIds, $categoryIds, $operator = 'OR')
	{
		if (!is_array($postIds)) {
			$postIds = array($postIds);
		}
		
		if (!is_array($categoryIds)) {
			$categoryIds = array($categoryIds);
		}
		
		$postSql = Mage::helper('wordpress/db')->getReadAdapter()->quoteInto("`main_table`.`ID` IN (?)", $postIds);
		$categorySql = Mage::helper('wordpress/db')->getReadAdapter()->quoteInto("`tax`.`term_id` IN (?)", $categoryIds);
		
		$this->joinTermTables('category');
		
		if (count($postIds) > 0 && count($categoryIds) > 0) {
			$this->getSelect()->where("{$postSql} {$operator} {$categorySql}");
		}
		else if (count($postIds) > 0) {
			$this->getSelect()->where("{$postSql}");
		}
		else if (count($categoryIds) > 0) {
			$this->getSelect()->where("{$categorySql}");	
		}

		return $this;	
	}


	/**
	 * Filters the collection by a category slug
	 *
	 * @param string $categorySlug
	 */
	public function addCategorySlugFilter($categorySlug)
	{
		return $this->joinTermTables('category')
			->addFieldToFilter('slug', $categorySlug);
	}

	/**
	  * Filter the collection by a category ID
	  *
	  * @param int $categoryId
	  * @return $this
	  */
	public function addCategoryIdFilter($categoryId)
	{
		return $this->addTermIdFilter($categoryId, 'category');
	}
	
	/**
	  * Filter the collection by a tag ID
	  *
	  * @param int $categoryId
	  * @return $this
	  */
	public function addTagIdFilter($tagId)
	{
		return $this->addTermIdFilter($tagId, 'post_tag');
	}
	
	/**
	 * Filters the collection with an archive date
	 * EG: 2010/10
	 *
	 * @param string $archiveDate
	 */
	public function addArchiveDateFilter($archiveDate, $isDaily = false)
	{
		if ($isDaily) {
			$this->getSelect()->where("`main_table`.`post_date` LIKE ?", str_replace("/", "-", $archiveDate)." %");
		}
		else {
			$this->getSelect()->where("`main_table`.`post_date` LIKE ?", str_replace("/", "-", $archiveDate)."-%");
		}
			
		return $this;	
	}
	
	/**
	 * If an Admin, add positon in product
	 *
	 */
	protected function _afterLoad()
	{
		if (Mage::getDesign()->getArea() == 'adminhtml') {
			if ($product = Mage::registry('product')) {
				foreach($this as $item) {
					$item->setPositionInProduct($item->getResource()->getPositionInProduct($item, $product->getId()));
				}
			}
		}
	
		return parent::_afterLoad();
	}
}

