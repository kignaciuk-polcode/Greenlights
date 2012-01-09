<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List extends Mage_Core_Block_Template
{
	/**
	 * Cache for post collection
	 *
	 * @var Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected $_postCollection = null;

	/**
	 * Block wrapper (category, tag, author etc)
	 *
	 * @var Fishpig_Wordpress_Block_Post_List_Abstract
	 */
	protected $_wrapperBlock = null;
	
	/**
	 * Amount of posts to display on a page
	 * If this is left null then this value is taken form the WP-Admin config
	 *
	 * @var int
	 */
	protected $_pagerLimit = null;
	
	/**
	 * Name of the pager block
	 *
	 * @var string
	 */
	protected $_pagerBlockName = 'wordpress_post_list_pager';
	
	/**
	 * Returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	public function getPosts()
	{
		return $this->_getPostCollection();
	}
	
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		if (is_null($this->_postCollection)) {
			if (is_null($this->getWrapperBlock()) == false) {
				$this->_postCollection = $this->getWrapperBlock()->getPostCollection();
			}
		}
		
		return $this->_postCollection;
	}
	
	/**
	 * Sets the parent block of this block
	 * This block can be used to auto generate the post list
	 *
	 * @param Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract $wrapper
	 */
	public function setWrapperBlock(Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract $wrapper)
	{
		$this->_wrapperBlock = $wrapper;
		return $this;
	}
	
	/**
	 * Returns the block wrapper object
	 *
	 * @return Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
	 */
	public function getWrapperBlock()
	{
		return $this->_wrapperBlock;
	}
	
	/**
	 * Get the pager block
	 * If the block isn't set in the layout XML, it will be created and will use the default template
	 *
	 * @return Fishpig_Wordpress_Post_List_Pager
	 */
	public function getPagerBlock()
	{
		$pagerBlock = $this->getChild($this->_pagerBlockName);
		
		if (!$pagerBlock) {
			$pagerBlock = $this->getLayout()
				->createBlock('wordpress/post_list_pager', $this->_pagerBlockName.microtime().rand(1,9999));
		}

		$pagerBlock->setLimit($this->_getPagerLimit())
			->setPageVarName('page')
			->setAvailableLimit($this->_getPagerAvailableLimit());
			
		$pagerBlock->setCollection($this->_getPostCollection());
		
		return $pagerBlock;
	}
	
	/**
	 * Get the HTML for the pager block
	 *
	 * @return string
	 */
	public function getPagerHtml()
	{
		return $this->getPagerBlock()->toHtml();
	}

	/**
	 * Gets the posts per page limit
	 *
	 * @return int
	 */
	protected function _getPagerLimit()
	{
		if (is_null($this->_pagerLimit)) {
			$this->_pagerLimit = $this->getRequest()->getParam('limit', Mage::helper('wordpress')->getCachedWpOption('posts_per_page', 10));
		}
		
		return (int)$this->_pagerLimit;
	}
	
	/**
	 * Returns the available limits for the pager
	 * As Wordpress uses a fixed page size limit, this returns only 1 limit (the value set in WP admin)
	 * This effectively hides the 'Show 4/Show 10' drop down
	 *
	 * @return array
	 */
	protected function _getPagerAvailableLimit()
	{
		return array($this->_getPagerLimit() => $this->_getPagerLimit());
	}
}
