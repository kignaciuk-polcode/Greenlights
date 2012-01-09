<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Block_Post_View_Abstract extends Mage_Core_Block_Template
{
	/**
	 * Returns the currently loaded post model
	 *
	 * @return Fishpig_Wordpress_Model_Post
	 */
	public function getPost($d = false)
	{
		if (!$this->hasData('post')) {
			if ($postId = $this->getData('post_id')) {
				$post = Mage::getModel('wordpress/post')->load($postId);
				
				if ($post->getId() == $postId) {
					$this->setData('post', $post);
				}
			}
			else {
				$this->setData('post', Mage::registry('wordpress_post'));
			}
		}
		
		return $this->getData('post');
	}

	/**
	 * Sets the current post object
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @param bool $replaceIfExists
	 */
	public function setPost(Fishpig_Wordpress_Model_Post $post, $replaceIfExists = true)
	{
		if (!$this->hasData('post') || $replaceIfExists) {
			$this->setData('post', $post);
		}
		
		return $this;
	}
	
	/**
	 * Returns the ID of the currently loaded post
	 *
	 * @return int
	 */
	public function getPostId()
	{
		if ($post = $this->getPost()) {
			return $post->getId();
		}
	}
	
	/**
	 * Returns true if comments are enabled for this post
	 */
	protected function canComment()
	{
		if ($post = $this->getPost()) {
			return $post->getCommentStatus() == 'open';
		}
		
		return false;
	}

	/**
	 * Wrapper for escapeHtml
	 * This is compatiable with Magento 1.3.2.4 and below
	 *
	 * @param string $html
	 * @return string
	 */
	public function escapeHtml($html, $allowedTags = null)
	{
		$helper = Mage::helper('core');

		if (method_exists($helper, 'htmlEscape')) {
			return $helper->htmlEscape($html, $allowedTags);
		}
		
		return $helper->escapeHtml($html, $allowedTags);
	}
}
