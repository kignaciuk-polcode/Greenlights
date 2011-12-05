<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_TagController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		parent::_init();

		if ($postTag = $this->_initPostTag()) {

			$this->_title(ucwords($postTag->getName()))
				->_addCrumb('tags', array('link' => $postTag->getAllTagsUrl(), 'label' => $this->__('Tags')))
				->_addCrumb('tag', array('link' => $postTag->getUrl(), 'label' => ucwords($postTag->getName())))
				->_addCanonicalLink($postTag->getUrl());
				
			if ($seo = $this->getSeoPlugin()) {
				if ($seo->getPluginOption('tags_noindex')) {
					if ($headBlock = $this->getLayout()->getBlock('head')) {
						$headBlock->setRobots('noindex,follow');
					}
				}
			}
			
			return true;				
		}

		$this->throwInvalidObjectException('tag');		
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
	 * Load user based on URI
	 *
	 * @return Fishpig_Wordpress_Model_User
	 */
	protected function _initPostTag()
	{
		$uri = Mage::helper('wordpress/router')->getBlogUri();
		$base = Mage::helper('wordpress/router')->getTagBase();
		
		if ($base) {
			if (substr($uri, 0, strlen($base)) == $base) {
				$uri = trim(substr($uri, strlen($base)), '/');
			}
		}

		$uri = urlencode($uri);
		
		if ($postTag = Mage::getModel('wordpress/post_tag')->load($uri, 'slug')) {
			if ($postTag->getId() > 0) {
				Mage::register('wordpress_post_tag', $postTag);
				return $postTag;
			}
		}

		return false;
	}
	
	/**
	 * List all tags with associated posts
	 *
	 */
	public function listAction()
	{
		parent::_init();
		
		$this->_title($this->__('Tag Archives'));
		$this->_addCrumb('tags', array('link' => Mage::helper('wordpress/post')->getTagsUrl(), 'label' => $this->__('Tags')));
		$this->_addCanonicalLink(Mage::helper('wordpress/post')->getTagsUrl());
		$this->renderLayout();
	}
}
