<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar_Widget_Posts extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Set the posts collection
	 *
	 */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();

		$this->setPosts($this->_getPostCollection());

		return $this;
	}
	
	/**
	 * Adds on cateogry/author ID filters
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		$collection = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter()
			->setOrderByPostDate()
			->setPageSize($this->getNumber() ? $this->getNumber() : 5)
			->setCurPage(1);

		if ($categoryId = $this->getCategoryId()) {
			$collection->addCategoryIdFilter($categoryId);
		}
		
		if ($authorId = $this->getAuthorId()) {
			$collection->addAuthorIdFilter($authorId);
		}

		return $collection;
	}
}
