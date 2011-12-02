<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_View_Comments extends Fishpig_Wordpress_Block_Post_View_Abstract
{
	/**
	 * Block name for the comments form block
	 *
	 * @var string
	 */
	protected $_commentsFormBlockName = 'wordpress_post_comment_form';

	/**
	 * Name of the pager block
	 *
	 * @var string
	 */
	protected $_pagerBlockName = 'wordpress_post_comment_pager';
	
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('wordpress/post/view/comments.phtml');
	}
	
	/**
	 * Returns a collection of comments for the current post
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	public function getComments()
	{
		if (!$this->hasComments()) {
			if ($post = $this->getPost()) {
				$this->setComments($post->getResource()->getPostComments($post));
			}
		}
		
		return $this->getData('comments');
	}
	
	/**
	 * Retrieve the amount of comments for the current post
	 *
	 * @return int
	 */
	public function getCommentCount()
	{
		if (!$this->hasCommentCount()) {
			$this->setCommentCount($this->getPost()->getResource()->getPostComments($this->getPost())->count());
		}
		
		return $this->getData('comment_count');
	}

	/**
	 * Setup the pager and comments form blocks
	 *
	 */
	protected function _beforeToHtml()
	{
		if ($pagerBlock = $this->getPagerBlock()) {
			$pagerBlock->setCollection($this->getComments());
		}

		if ($commentsFormBlock = $this->getCommentFormBlock()) {
			$commentsFormBlock->setPost($this->getPost());
		}

		parent::_beforeToHtml();
	}
	
	/**
	 * Returns the HTML for the comment form
	 *
	 * @return string
	 */
	public function getCommentFormHtml()
	{
		return $this->getChildHtml($this->_commentsFormBlockName);
	}
	
	/**
	 * Gets a block for the comment form
	 *
	 * @return Fishpig_Wordpress_Block_Post_View_Comment_Form
	 */
	public function getCommentFormBlock()
	{
		if (!$this->getChild($this->_commentsFormBlockName)) {
			$this->setChild($this->_commentsFormBlockName, $this->getLayout()->createBlock('wordpress/post_view_comment_form'));
		}

		return $this->getChild($this->_commentsFormBlockName);
	}
	
	/**
	 * Get the pager block
	 * If the block isn't set in the layout XML, it will be created and will use the default template
	 *
	 * @return Fishpig_Wordpress_Post_Comment_Pager
	 */
	public function getPagerBlock()
	{
		if (!$this->getChild($this->_pagerBlockName)) {
			$this->setChild($this->_pagerBlockName, $this->getLayout()->createBlock('wordpress/post_view_comment_pager'));
		}
		
		return $this->getChild($this->_pagerBlockName);
	}
	
	/**
	 * Get the HTML for the pager block
	 *
	 * @return null|string
	 */
	public function getPagerHtml()
	{
		if ($this->helper('wordpress')->getCachedWpOption('page_comments', false)) {
			return $this->getChildHtml($this->_pagerBlockName);
		}
	}
}
