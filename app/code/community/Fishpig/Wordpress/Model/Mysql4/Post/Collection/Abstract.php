<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Mysql4_Post_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * True if term tables have been joined
	 * This stops the term tables being joined repeatedly
	 *
	 * @var array()
	 */
	protected $_termTablesJoined = array();
	
	/**
	 * Return the current post type (post || page)
	 *
	 * @return string
	 */
	protected function _getPostType()
	{
		return trim(substr($this->getResourceModelName(), strpos($this->getResourceModelName(), '/')), '/');
	}
	
	/**
	 * Ensures that only posts and not pages are returned
	 * WP stores posts and pages in the same DB table
	 *
	 */
    protected function _initSelect()
    {
    	parent::_initSelect();

        $this->getSelect()->where("`main_table`.`post_type`=?", $this->_getPostType());

		return $this;
	}

	/**
	 * Adds a published filter to collection
	 *
	 */
	public function addIsPublishedFilter()
	{
		return $this->addStatusFilter('publish');
	}
	
	/**
	 * Adds a filter to the status column
	 *
	 * @param string $status
	 */
	public function addStatusFilter($status)
	{
		if (is_array($status)) {
			$this->getSelect()	->where('`main_table`.`post_status` IN (?)', $status);
		}
		else {
			$this->getSelect()	->where('`main_table`.`post_status` =?', $status);
		}
			
		return $this;
	}
	
	/**
	 * Sets the current page based on the URL value
	 */
	public function setPageFromUrl()
	{
		$pageId = Mage::app()->getRequest()->getParam('page', 1);
		return $this->setCurPage($pageId);
	}
	
	/**
	 * Sets the number of posts per page
	 * If no value is passed, the number of posts is taken from the WP Admin Config
	 *
	 * @param int $postsPerPage
	 */
	public function setPostsPerPage($postsPerPage = null)
	{
		if (is_null($postsPerPage)) {
			$postsPerPage = Mage::app()->getRequest()->getParam('limit', Mage::helper('wordpress')->getCachedWpOption('posts_per_page', 10));
		}

		return $this->setPageSize($postsPerPage);
	}
	
	/**
	 * Filter the collection by an author ID
	 *
	 * @param int $authorId
	 */
	public function addAuthorIdFilter($authorId)
	{
		return $this->addFieldToFilter('post_author', $authorId);
	}
	
	/**
	 * Orders the collection by post date
	 *
	 * @param string $dir
	 */
	public function setOrderByPostDate($dir = 'desc')
	{
		return $this->setOrder('post_date', $dir);
	}
	
	/**
	 * Filter the collection by a date
	 *
	 * @param string $dateStr
	 */
	public function addPostDateFilter($dateStr)
	{
		if (strpos($dateStr, '%') !== false) {
			$this->addFieldToFilter('post_date', array('like' => $dateStr));
		}
		else {
			$this->addFieldToFilter('post_date', $dateStr);
		}
		
		return $this;
	}
	
	/**
	 * Filters the collection by an array of words on the array of fields
	 *
	 * @param array $words - words to search for
	 * @param array $fields - fields to search
	 * @param string $operator
	 */
	public function addSearchStringFilter(array $words, array $fields, $operator)
	{
		if (count($words) > 0) {
			$read = Mage::helper('wordpress/db')->getReadAdapter();
			$where = array();
	
			foreach($fields as $field) {
				foreach($words as $word) {
					$where[] = $read->quoteInto("{$field} LIKE ? ", "%{$word}%");
				}
			}
	
			$this->getSelect()->where(implode(" {$operator} ", $where));
		}
		else {
			$this->getSelect()->where('1=2');
		}
		
		return $this;
	}
	
	/**
	 * Filters the collection by a term ID and type
	 *
	 * @param int|array $termId
	 * @param string $type
	 */
	public function addTermIdFilter($termId, $type)
	{
		$this->joinTermTables($type);
		
		if (is_array($termId)) {
			$this->getSelect()->where("`tax`.`term_id` IN (?)", $termId);
		}
		else {
			$this->getSelect()->where("`tax`.`term_id` = ?", $termId);
		}

		return $this;
	}

	/**
	 * Joins the category tables to the collection
	 * This allows filtering by category
	 */
	public function joinTermTables($type)
	{
		if (!isset($this->_termTablesJoined[$type])) {
			$tableTax = Mage::helper('wordpress/db')->getTableName('term_taxonomy');
			$tableTermRel	 = Mage::helper('wordpress/db')->getTableName('term_relationships');
			$tableTerms = Mage::helper('wordpress/db')->getTableName('terms');
			
			$this->getSelect()->join(array('rel' => $tableTermRel), "`rel`.`object_id`=`main_table`.`ID`", '');
			$this->getSelect()->join(array('tax' => $tableTax), "`tax`.`term_taxonomy_id`=`rel`.`term_taxonomy_id` AND `tax`.`taxonomy`='{$type}'", '');
			$this->getSelect()->join(array('terms' => $tableTerms), "`terms`.`term_id` = `tax`.`term_id`", '');
			$this->getSelect()->distinct();
			
			$this->_termTablesJoined[$type] = true;
		}

		return $this;
	}
	
	/**
	 * Add post parent ID filter
	 *
	 * @param int $postParentId
	 */
	public function addPostParentIdFilter($postParentId)
	{
		$this->getSelect()->where("main_table.post_parent=?", $postParentId);
		
		return $this;
	}
	
	/**
	 * Order the collection by the menu order field
	 *
	 * @param string $dir
	 * @return
	 */
	public function orderByMenuOrder($dir = 'asc')
	{
		$this->getSelect()->order('menu_order ' . $dir);
		
		return $this;
	}
}
