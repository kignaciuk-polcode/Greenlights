<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Mysql4_Category_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Defines the type of category
	 * Can be either category or link_category
	 *
	 * @var string
	 */
	protected $_categoryType = 'category';

	/**
	 * Perform the joins necessary to create a full category record
	 */
	protected function _initSelect()
	{
		return $this->getSelect()
			->distinct()
			->from(array('main_table' => $this->getResource()->getMainTable()))
			->join(
				array('tax' => Mage::helper('wordpress/db')->getTableName('term_taxonomy')),
				Mage::helper('wordpress/db')->getWordpressRead()->quoteInto("`tax`.`taxonomy` = ?", $this->_categoryType)
				. " AND `main_table`.`term_id` = `tax`.`term_id`",
				array('parent_id' => 'parent', 'description', 'count')
			)
			->joinLeft(
				array('rel' => Mage::helper('wordpress/db')->getTableName('term_relationships')),
				"`tax`.`term_taxonomy_id` = `rel`.`term_taxonomy_id`",
				''
			);
	}
	
	/**
	 * Filters the collection by parent
	 * If 0 is passed, root categories will be returned
	 *
	 * @var int $parentId
	 */
	public function addParentIdFilter($parentId)
	{
		return $this->addFieldToFilter('parent', $parentId);
	}

	/**
	 * Filter the collection by a post ID
	 *
	 * @param int $postId
	 */
	public function addPostIdFilter($postId)
	{
		return $this->addFieldToFilter('object_id', $postId);
	}
	
	/**
	 * Filter the collection by a post model
	 * This is just a wrapper for self::addPostIdFilter
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 */
	public function addPostFilter(Fishpig_Wordpress_Model_Post $post)
	{
		return $this->addPostIdFilter($post->getId());
	}
	
	/**
	 * Order the collection by the count field
	 *
	 * @param string $dir
	 */
	public function addOrderByCount($dir = 'desc')
	{
		$this->getSelect()->order('tax.count ' . $dir);
		return $this;
	}
}
