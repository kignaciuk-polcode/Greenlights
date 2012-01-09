<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Post_ViewController extends Fishpig_Wordpress_Controller_Abstract
{
	protected function _init()
	{
		if (!$this->_initPostModel()) {
			return false;
		}

		if ($this->isFeedPage()) {
			$this->_forward('commentFeed');
			return null;
		}
		
		$this->_checkForPostedComment();
		
		parent::_init();
		
		if ($post = $this->_getPost()) {
			$this->_title($post->getPageTitle());
				
			if ($headBlock = $this->getLayout()->getBlock('head')) {
				if ($post->getMetaDescription()) {
					$headBlock->setDescription($post->getMetaDescription());
				}
				
				if ($post->getMetaKeywords()) {
					$headBlock->setKeywords($post->getMetaKeywords());
				}
			}
			
			$this->_addCrumb('post', array('label' => $post->getPostTitle()));
			$this->_addCanonicalLink($post->getPermalink());
			
			if ($headBlock = $this->getLayout()->getBlock('head')) {
				$feedTitle = sprintf('%s &raquo; %s Comments Feed', Mage::helper('wordpress')->getCachedWpOption('blogname'), $post->getPostTitle());
				$headBlock->addItem('link_rel', $post->getCommentFeedUrl(), 'rel="alternate" type="application/rss+xml" title="' . $feedTitle . '"');
			}
		}

		return true;
	}

	/**
	 * Sets a custom root template (if set)
	 *
	 * @return Fishpig_Wordpress_Controller_Abstract
	 */
	public function setCustomRootTemplate()
	{
		if ($template = Mage::getStoreConfig('wordpress_blog/layout/template_post_list')) {
			if ($this->_setCustomRootTemplate($template)) {
				return $this;
			}
		}

		return parent::setCustomRootTemplate();
	}
	
	/**
	 * Returns the current post model
	 *
	 * @return Fishpig_Wordpress_Model_Post
	 */
	protected function _getPost()
	{
		return Mage::registry('wordpress_post');
	}

	/**
	 * Check whether a comment has been posted
	 *
	 */
	protected function _checkForPostedComment()
	{
		if ($response = $this->getRequest()->getParam('cy')) {
			Mage::getSingleton('core/session')->addSuccess($this->__(Mage::getStoreConfig('wordpress_blog/post_comments/success_msg')));
		}
		else if ($response = $this->getRequest()->getParam('cx')) {
			Mage::getSingleton('core/session')->addError($this->__(Mage::getStoreConfig('wordpress_blog/post_comments/error_msg')));
		}

		return $this;
	}

	/**
	 * Display the comment feed
	 *
	 */
	public function commentFeedAction()
	{
		if ($this->isEnabledForStore()) {
			$this->getResponse()
				->setBody($this->getLayout()->createBlock('wordpress/feed_post_comment')->setPost($this->_getPost())->toHtml());

			$this->getResponse()->sendResponse();
			exit;
		}
		else {
			$this->_forward('noRoute');
		}
	}
	
	/**
	 * Initialise the post model
	 * Provides redirects for Guid links when using permalinks
	 *
	 * @return false|Fishpig_Wordpress_Model_Post
	 */
	protected function _initPostModel()
	{
		$postHelper = Mage::helper('wordpress/post');
		$isPreview = $this->getRequest()->getParam('preview', false);
		
		if (!$postHelper->useGuidLinks()) {
			$uri = Mage::helper('wordpress/router')->getBlogUri();

			if ($post = $postHelper->loadByPermalink($uri)) {
				if ($this->getRequest()->getParam($this->getRouterHelper()->getTrackbackVar())) {
					$this->_redirectUrl($post->getUrl());
					$this->getResponse()->sendHeaders();
					exit;
				}

				if ($post->isPublished()) {
					Mage::register('wordpress_post', $post);
					return $post;
				}
				
				return false;
			}

			if ($postId = $postHelper->getPostId()) {
				$post = Mage::getModel('wordpress/post')->load($postId);

				if ($post->getId()) {
					if ($isPreview) {
						Mage::register('wordpress_post', $post);
						return $post;
					}

					if ($post->isPublished()) {
						$this->_redirectUrl($post->getUrl());
						$this->getResponse()->sendHeaders();
						exit;
					}
				}
			}
		}
		else if ($postId = $postHelper->getPostId()) {
			$post = Mage::getModel('wordpress/post')->load($postId);
			
			if ($post->getId()) {
				if ($post->isPublished() || $isPreview) {
					Mage::register('wordpress_post', $post);
					return $post;
				}
			}
		}
		
		return false;
	}
}
