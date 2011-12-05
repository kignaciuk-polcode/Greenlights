<?php

class Fishpig_Wordpress_Helper_Router extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * The variable used for pages
	 *
	 * @var string
	 */
	protected $_postPagerVar = 'page';
	
	/**
	 * The variable format used for comment pages
	 *
	 * @var string
	 */
	protected $_commentPagerVarFormat = '^comment-page-%s$';
	
	/**
	 * The variable used to indicate this is a feed page
	 *
	 * @var string
	 */
	protected $_feedVar = 'feed';
	
	/**
	 * The variable used to indicate a trackback page
	 *
	 * @var string
	 */
	protected $_trackbackVar = 'trackback';
	
	/**
	 * Retrieve the blog URI
	 * This is the whole URI after blog route
	 *
	 * @return string
	 */
	public function getBlogUri()
	{
		$pathInfo = explode('/', trim($this->getRequest()->getPathInfo(), '/'));
		
		if (count($pathInfo) == 0) {
			return null;
		}
		
		if ($pathInfo[0] != $this->getBlogRoute()) {
			return null;
		}

		// Remove blog route
		array_shift($pathInfo);
		
		// Clean off pager and feed parts
		if (($key = array_search($this->getPostPagerVar(), $pathInfo)) !== false) {
			if (isset($pathInfo[($key+1)]) && preg_match("/[0-9]{1,}/", $pathInfo[($key+1)])) {
				$this->getRequest()->setParam($this->getPostPagerVar(), $pathInfo[($key+1)]);
				unset($pathInfo[($key+1)]);
				unset($pathInfo[$key]);
				
				$pathInfo = array_values($pathInfo);
			}
		}
		
		// Clean off feed and trackback variable
		foreach(array($this->getFeedVar(), $this->getTrackbackVar()) as $var) {
			if (($key = array_search($var, $pathInfo)) !== false) {
				unset($pathInfo[$key]);
				$pathInfo = array_values($pathInfo);
				$this->getRequest()->setParam($var, 1);
			}
		}
		
		// Remove comments pager variable
		foreach($pathInfo as $i => $part) {
			$results = array();
			if (preg_match("/" . sprintf($this->getCommentPagerVarFormat(), '([0-9]{1,})') . "/", $part, $results)) {
				if (isset($results[1])) {
					unset($pathInfo[$i]);
				}
			}
		}
		
		if (count($pathInfo) == 1 && preg_match("/^[0-9]{1,8}$/", $pathInfo[0])) {
			$this->getRequest()->setParam(Mage::helper('wordpress/post')->getPostIdVar(), $pathInfo[0]);
			
			array_shift($pathInfo);
		}

		return urldecode(implode('/', $pathInfo));
	}
	
	/**
	 * Determines whether the uri is a blog archive URI
	 *
	 * @param string $uri
	 * @return bool
	 */
	public function isArchiveUri($uri)
	{
		$pattern['year'] = '[1-2]{1}[0-9]{3}';
		$pattern['month'] = '[0-1]{1}[0-9]{1}';
		$pattern['day'] = '[0-3]{1}[0-9]{1}';

		return preg_match("/^" . implode('\/', $pattern) . "$/", $uri)
			|| preg_match("/^" . $pattern['year'] . '\/' . $pattern['month'] . '$/', $uri);
	}
	
	/**
	 * Determine whether the URI is a blog author uri
	 *
	 * @param string $uri
	 * @return bool
	 */
	public function isAuthorUri($uri)
	{	
		return preg_match('/^author\/' . $this->getPermalinkStringRegex() . '$/i', $uri);
	}
	
	/**
	 * Determine whether the URI is a blog category URI
	 *
	 * @param string
	 * @return bool
	 */
	public function isCategoryUri($uri)
	{
		if (Mage::helper('wordpress')->isPluginEnabled('No Category Base')) {
			$category = Mage::getModel('wordpress/post_category')->loadBySlug($uri);
			return $category && $category->getId();
		}

		return preg_match('/^' . Mage::helper('wordpress/router')->getCategoryBase() . '\/' . $this->getPermalinkStringRegex() . '$/i', $uri);
	}
	
	/**
	 * Determine whether the URI is a blog tag URI
	 *
	 * @param string $uri
	 * @return bool
	 */
	public function isTagUri($uri)
	{
		return preg_match('/^' . Mage::helper('wordpress/router')->getTagBase() . '\/' . $this->getPermalinkStringRegex() . '$/', $uri);
	}
	
	/**
	 * Determine whether the URI is a blog post URI
	 *
	 * @param string $uri
	 * @return bool
	 */
	public function isPostUri($uri)
	{
		return Mage::helper('wordpress/post')->isPostUri($uri);
	}
	
	/**
	 * Determine whether the URI is a blog post attachment URI
	 *
	 * @param string $uri
	 * @return bool
	 */
	public function isPostAttachmentUri($uri)
	{
		return Mage::helper('wordpress/post')->isPostAttachmentUri($uri);
	}
	
	/**
	 * Determine whether the URL is a page URI
	 *
	 * @param string $uri
	 * @param bool $registerPage = false
	 * @return bool
	 */
	public function isPageUri($uri, $registerPage = false)
	{
		$uris = explode('/', $uri);
		$pages = array();
		$count = 0;
		
		foreach($uris as $uri) {
			$page = Mage::getModel('wordpress/page')->loadBySlug($uri);
			
			if (!$page->getId()) {
				return false;
			}

			if ($count++ > 0) {
				$lastPage = end($pages);
				$page->setParentPage($lastPage);
				reset($pages);
			}
			else {
				if ($page->getPostParent() > 0) {
					return false;
				}
			}
			
			$pages[] = $page;
		}
		
		if ($registerPage) {
			$page = array_pop($pages);
			Mage::register('wordpress_page', $page, true);
		}
		
		return true;
	}
	
	/**
	 * Trim the base from the URI
	 *
	 * @param string $uri
	 * @param string $base
	 * @param string $ltrim
	 * @return string
	 */
	public function trimUriBase($uri, $base, $ltrim = '/')
	{
		if (substr($uri, 0, strlen($base)) == $base) {
			$uri = substr($uri, strlen($base));
			
			if (!is_null($ltrim)) {
				$uri = ltrim($uri, $ltrim);
			}
		}
		
		return $uri;
	}
	
	/**
	 * Retrieve the URI with the base portion trimmed off
	 *
	 * @param string $base
	 * @param string $ltrim
	 * @return string
	 */
	public function getTrimmedUri($base, $ltrim = '/')
	{
		return $this->trimUriBase($this->getBlogUri(), $base, $ltrim);
	}
	
	/**
	 * Retrieve the category base
	 *
	 * @return string
	 */
	public function getCategoryBase()
	{
		return Mage::helper('wordpress')->getCachedWpOption('category_base', 'category');
	}
	
	/**
	 * Retrieve the tag base
	 *
	 * @return string
	 */
	public function getTagBase()
	{
		return Mage::helper('wordpress')->getCachedWpOption('tag_base', 'tag');
	}
	
	/**
	 * Retrieve the Regex pattern used to identify a permalink string
	 * Allows for inclusion of other locale characters
	 *
	 * @return string
	 */
	public function getPermalinkStringRegex()
	{
		return '[a-z0-9' . $this->getSpecialUriChars() . '_\-\.]{1,}';
	}

	/**
	 * Retrieve an array of special chars that can be used in a URI
	 *
	 * @return array
	 */
	public function getSpecialUriChars()
	{
		$chars = array('‘', '’','“', '”', '–', '—', '`');
		
		if (Mage::helper('wordpress')->isCryllicLocaleEnabled()) {
			$chars[] = '\p{Cyrillic}';
		}
			
		return implode('', $chars);	
	}
	
	/**
	 * Retrieve the format variable for the comment pager
	 *
	 * @return string
	 */
	public function getCommentPagerVarFormat()
	{
		return $this->_commentPagerVarFormat;
	}
	
	/**
	 * Retrieve the post pager variable
	 *
	 * @return string
	 */
	public function getPostPagerVar()
	{
		return $this->_postPagerVar;
	}
	
	/**
	 * Retrieve the feed variable
	 *
	 * @return string
	 */
	public function getFeedVar()
	{
		return $this->_feedVar;
	}
	
	/**
	 * Retrieve the trackback variable
	 *
	 * @return string
	 */
	public function getTrackbackVar()
	{
		return $this->_trackbackVar;
	}
	
	/**
	 * Retrieve the request object
	 *
	 * @return
	 */
	public function getRequest()
	{
		return Mage::app()->getRequest();
	}
}
