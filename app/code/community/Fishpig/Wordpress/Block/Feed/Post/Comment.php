<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Feed_Post_Comment extends Fishpig_Wordpress_Block_Feed_Abstract
{
	public function __construct()
	{
		$this->setTemplate('wordpress/feed/post/comment.phtml');
	}

	/**
	 * Retrieve a collection of posts for the feed
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Comment_Collection
	 */
	public function getComments()
	{
		return $this->getPost()->getResource()->getPostComments($this->getPost());
	}
	
	/**
	 * Retrieve the post
	 *
	 * @return Fishpig_Wordpress_Model_Post
	 */
	public function getPost()
	{
		return Mage::registry('wordpress_post');
	}

	/**
	 * Retrieve the feed URL
	 *
	 * @return string
	 */
	public function getFeedUrl()
	{
		return rtrim($this->getPost()->getUrl(), '/') . '/feed/';
	}
}
