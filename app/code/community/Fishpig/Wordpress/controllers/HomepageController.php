<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_HomepageController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * Initialise the homepage
	 *
	 */
	protected function _init()
	{
		$this->_handleHomepageRedirect();

		parent::_init();
		
		$this->_addCanonicalLink(Mage::helper('wordpress')->getUrl());
		
		if ($this->getSeoPlugin()->isEnabled()) {
			if ($title = $this->getSeoPlugin()->getPluginOption('home_title')) {
				$this->_title()->_title($title);
			}
		}
		
		return true;
	}

	protected function _handleHomepageRedirect()
	{
		if ($redirectCode = Mage::getStoreConfig('wordpress_blog/layout/blog_as_homepage_redirect')) {
			if (Mage::helper('wordpress')->isBlogMagentoHomepage()) {
				if ($this->isBlogUrl()) {
					$this->_redirectUrlNow(Mage::getUrl(), $redirectCode);
					exit;
				}
			}
			else {
				if (!$this->isBlogUrl()) {
					$this->_redirectUrlNow(Mage::helper('wordpress')->getUrl(), $redirectCode);
					exit;
				}
			}
		}
	}

	/**
	 * Determine wether the current URL is the actual blog URL
	 *
	 * @return bool
	 */
	public function isBlogUrl()
	{
		$currentUrl = str_replace('/index.php', '', rtrim($this->getCurrentUrl(), '/'));
		$blogUrl = str_replace('/index.php', '', rtrim(Mage::helper('wordpress')->getUrl(), '/'));

		return (strpos($currentUrl, $blogUrl) !== false);
	}
	
	/**
	 * Sets a custom root template (if set)
	 *
	 * @return Fishpig_Wordpress_Controller_Abstract
	 */
	public function setCustomRootTemplate()
	{
		if ($template = Mage::getStoreConfig('wordpress_blog/layout/template_homepage')) {
			if ($this->_setCustomRootTemplate($template)) {
				return $this;
			}
		}

		return parent::setCustomRootTemplate();
	}
	
	/**
	 * If not feed, display the blog homepage
	 *
	 */
	public function indexAction()
	{
		if ($this->isFeedPage()) {
			$this->_forward('index', 'feed');
		}
		else {
			parent::indexAction();
		}
	}
}
