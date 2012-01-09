<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_CategoryController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		if ($category = $this->_loadCategoryBasedOnUrl()) {
		
			if ($this->isFeedPage()) {
				$this->_forward('commentFeed');
				return null;
			}

			if (!Mage::helper('wordpress')->isLegacyMagento()) {
				$this->_addCustomLayoutHandles(array('wordpress_category_index', 'WORDPRESS_CATEGORY_'.$category->getId()));
			}
			
			// Add base breacrumbs and title
			parent::_init();
			
			$this->_title($category->getName());
			$this->_addCrumb('category', array('link' => $category->getUrl(), 'label' => $category->getName()));
			$this->_addCanonicalLink($category->getUrl());

			if ($seo = $this->getSeoPlugin()) {
				if ($seo->getPluginOption('category_noindex')) {
					if ($headBlock = $this->getLayout()->getBlock('head')) {
						$headBlock->setRobots('noindex,follow');
					}
				}
			}
			
			return true;
		}

		$this->throwInvalidObjectException('category');
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
	 * Load the category based on the slug stored in the param 'category'
	 *
	 * @return Fishpig_Wordpress_Model_Post_Categpry
	 */
	protected function _loadCategoryBasedOnUrl()
	{
		$uri = Mage::helper('wordpress/router')->getBlogUri();

		if (!Mage::helper('wordpress')->isPluginEnabled('No Category Base')) {
			$base = Mage::helper('wordpress/router')->getCategoryBase();
			$slug = trim(substr($uri, strlen($base)), '/');
		}
		else {
			$slug = $uri;
		}

		$category = Mage::getModel('wordpress/post_category')->loadBySlug($slug);
			
		if ($category && $category->getId()) {
			Mage::register('wordpress_category', $category);
			return $category;
		}

		return false;
	}
	
	/**
	 * Display the comment feed
	 *
	 */
	public function commentFeedAction()
	{
		if ($this->isEnabledForStore()) {
			$this->getResponse()
				->setBody($this->getLayout()->createBlock('wordpress/feed_category')->setCategory(Mage::registry('wordpress_category'))->toHtml());

			$this->getResponse()->sendResponse();
			exit;
		}
		else {
			$this->_forward('noRoute');
		}
	}
}
