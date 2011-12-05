<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_View extends Fishpig_Wordpress_Block_Post_View_Abstract
{
	/**
	 * The block name for the comments block
	 *
	 * @var string
	 */
	protected $_commentsBlockName = 'wordpress_post_comments';
	
	/**
	  * Returns the HTML for the comments block
	  *
	  * @return string
	  */
	public function getCommentsHtml()
	{
		return $this->getChildHtml($this->_commentsBlockName);
	}
	
	/**
	 * Gets the comments block
	 *
	 * @return Fishpig_Wordpress_Block_Post_View_Comments
	 */
	public function getCommentsBlock()
	{
		if (!$this->getChild($this->_commentsBlockName)) {
			$this->setChild($this->_commentsBlockName, $this->getLayout()->createBlock('wordpress/post_view_comments'));
		}
		
		return $this->getChild($this->_commentsBlockName);
	}

	/**
	 * Setup the comments block
	 *
	 */
	protected function _beforeToHtml()
	{
		if ($commentsBlock = $this->getCommentsBlock()) {
			$commentsBlock->setPost($this->getPost());
		}
		
		return parent::_beforeToHtml();
	}
}
