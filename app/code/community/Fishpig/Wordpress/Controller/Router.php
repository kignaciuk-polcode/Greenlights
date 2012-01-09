<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
	/**
	 * Stores the static routes used by WordPress
	 *
	 * @var array
	 */
	protected $_staticRoutes = array();
	
	/**
	 * Used to provide classwide access to the request object
	 *
	 * @var null|Zend_Controller_Request_Http
	 */
	protected $_requestObject = null;
	
	/**
	 * Used to load in controller files
	 *
	 * @param string
	 */
	protected $_controllerClassPrefix = 'Fishpig_Wordpress';
	
	/**
	 * Create an instance of the router and add it to the queue
	 */
    public function initController($observer)
    {
    	$helper = Mage::helper('wordpress');
    	
    	if ($helper->isEnabled()) {
	        $front = $observer->getEvent()->getFront();

    	    $wp = new Fishpig_Wordpress_Controller_Router();
        	$front->addRouter('fishpig_wordpress', $wp);
        }
    }

	/**
	 * Attempt to match the current URI to this module
	 * If match found, set module, controller, action and dispatch
	 *
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function match(Zend_Controller_Request_Http $request)
	{
		try {
			if (Mage::helper('wordpress')->isFullyIntegrated() && Mage::app()->getStore()->getCode() !== 'admin') {
				$uri = Mage::helper('wordpress/router')->getBlogUri();
				$this->_requestObject = $request;

				if (!is_null($uri)) {
					if ($this->_match($uri)) {
						return $this->forceDispatch();
					}
				}
			}
		}
		catch (Exception $e) { 
			Mage::helper('wordpress')->log('Router: ' . $e->getMessage());
		}

		return false;
	}
	
	/**
	 * Performs the logic for self::match
	 *
	 * @param array $explodedUri
	 * @return bool
	 */
	protected function _match($uri)
	{
		$helper = Mage::helper('wordpress/router');
		$this->_initStaticRoutes();

		$this->getRequest()->setModuleName('wordpress')->setRouteName('wordpress');
		
		Mage::dispatchEvent('wordpress_match_routes_before', array('router' => $this, 'uri' => $uri));

		if ($this->getRequest()->getModuleName() && $this->getRequest()->getControllerName() && $this->getRequest()->getActionName()) {
			return true;
		}
		
		if (!$uri && !Mage::helper('wordpress/post')->getPostId()) {
			return $this->getRequest()->setControllerName('homepage')->setActionName('index');
		}
		else if ($this->_getRouteByAlias($uri)) {
			if (list($controller, $action) = $this->_getRouteByAlias($uri)) {
				return $this->getRequest()->setControllerName($controller)->setActionName($action);
			}
		}
		else if ($helper->isArchiveUri($uri)) {
			return $this->getRequest()->setControllerName('archive_view')->setActionName('index');
		}
		else if ($helper->isTagUri($uri)) {
			return $this->getRequest()->setControllerName('tag')->setActionName('index');
		}
		else if ($helper->isAuthorUri($uri)) {
			return $this->getRequest()->setControllerName('author')->setActionName('index');
		}
		else if ($helper->isCategoryUri($uri)) {
			return $this->getRequest()->setControllerName('category')->setActionName('index');
		}
		else if ($helper->isPageUri($uri, true)) {
			return $this->getRequest()->setControllerName('page_view')->setActionName('index');
		}
		else if ($helper->isPostUri($uri)) {
			return $this->getRequest()->setControllerName('post_view')->setActionName('index');
		}
		else if ($helper->isPostAttachmentUri($uri)) {
			return $this->_redirectFromAttachmentUriToPost($uri);
		}

		Mage::dispatchEvent('wordpress_match_routes_after', array('router' => $this));
		
		return false;
	}
	
	/**
	 * Adds redirects from attachment pages to parent post
	 * This stops 404 errors showing up in Google Analytics
	 *
	 * @param string $uri
	 * @return bool
	 */
	protected function _redirectFromAttachmentUriToPost($uri)
	{
		if (strpos($uri, '/') !== false) {
			$postUri = substr($uri, 0, strrpos($uri, '/'));
			
			header("HTTP/1.1 301 Moved Permanently");
			header('Location: ' . Mage::helper('wordpress')->getUrl($postUri));
			exit;
		}
		
		return false;
	}

	/**
	 * Dispatch the controller request
	 *
	 * @return bool
	 */
	protected function forceDispatch()
	{
		if ($controllerClassName = $this->_validateControllerClassName($this->_controllerClassPrefix, $this->getRequest()->getControllerName())) {
			$controllerInstance = new $controllerClassName($this->getRequest(), $this->getFront()->getResponse());

			if ($controllerInstance->hasAction($this->getRequest()->getActionName())) {
				$this->getRequest()->setDispatched(true);
				$controllerInstance->dispatch($this->getRequest()->getActionName());
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Initliase the static routes used by WordPress
	 *
	 */
	protected function _initStaticRoutes()
	{
		$this->addStaticRoute('author', 'author')
			->addStaticRoute(Mage::helper('wordpress')->getCachedWpOption('tag_base', 'tag'), 'tag')
			->addStaticRoute('post_view_comment', 'post_view_comment')
			->addStaticRoute('feed', 'feed')
			->addStaticRoute('sitemap.xml', 'sitemap')
			->addStaticRoute('tags', 'tag', 'list')
			->addStaticRoute('wp-admin', 'redirect', 'wpAdmin')
			->addStaticRoute('post-comment', 'post_view_comment', 'post')
			->addStaticRoute('robots.txt', 'robot');
		
		if (Mage::helper('wordpress/search')->isEnabled()) {
			$this->addStaticRoute(Mage::helper('wordpress/search')->getSearchRoute(), 'search');
		}
			
		Mage::dispatchEvent('wordpress_init_static_routes_after', array('router' => $this));
		
		return $this;
	}
	
	/**
	 * Register a static route
	 *
	 * @param string $route - internal token used by the controller
	 * @param string $alias - route used in the URL
	 */
	public function addStaticRoute($alias, $controller, $action = 'index')
	{
		$this->_staticRoutes[trim($alias, '/')] = array($controller, $action);
		return $this;
	}
	
	/**
	 * Returns the route for the given alias
	 *
	 * @param string $alias
	 * @return string
	 */
	protected function _getRouteByAlias($alias)
	{	
		if (strpos($alias, '/') !== false) {
			$alias = substr($alias, 0, strpos($alias, '/'));
		}

		return isset($this->_staticRoutes[$alias]) ? $this->_staticRoutes[$alias] : false;
	}
	
	/**
	 * Returns the current request object
	 *
	 * @return Zend_Controller_Request_Http
	 */
	public function getRequest()
	{
		return $this->_requestObject;
	}
	
	/**
	 * Set the controller class prefix
	 *
	 * @param string $prefix
	 * @return $this
	 */
	public function setControllerClassPrefix($prefix)
	{
		$this->_controllerClassPrefix = $prefix;
		
		return $this;
	}
}
