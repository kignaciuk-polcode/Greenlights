<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Abstract extends Mage_Core_Helper_Abstract
{
	/**
	 * Internal cache variable
	 *
	 * @var array
	 */
	protected $_cache = array();
	
	/**
	  * Returns the URL used to access your Wordpress frontend
	  *
	  * @param string|null $extra = null
	  * @param array $params = array
	  * @return string
	  */
	public function getUrl($extra = null, array $params = array())
	{
		if (count($params) > 0) {
			$extra = trim($extra, '/') . '/';
			
			foreach($params as $key => $value) {
				$extra .= $key . '/' . $value . '/';
			}
		}
		
		if ($this->isFullyIntegrated()) {
			$url = Mage::getUrl('', array(
				'_direct' 	=> $this->getBlogRoute() . '/' . ltrim($extra, '/'), 
				'_store' 	=> $this->_getStoreForConfig(),
				'_secure' 	=> false,
				'_nosid' 	=> true,
			));
		}
		else {
			$url = $this->getHomeUrl() . '/' . ltrim($extra, '/');
		}
	
		return htmlspecialchars($url);
	}
	
	public function getHomeUrl()
	{
		return $this->getCachedWpOption('home');
	}
	
	/**
	  * Returns the URL Wordpress is installed on
	  *
	  * @param string $extra
	  * @return string
	  */
	public function getBaseUrl($extra = '')
	{
		return rtrim($this->getCachedWpOption('siteurl'), '/') . '/' . $extra;
	}
	
	/**
	  * Get Wordpress Admin URL
	  *
	  */
	public function getAdminUrl($extra = null)
	{
		return $this->getBaseUrl('wp-admin/' . $extra);
	}
	
	/**
	  * Returns the blog route selected in the Magento config
	  *
	  * Returns null if full integration is disbaled
	  *
	  */
	public function getBlogRoute()
	{
		if (!isset($this->_cache['blog_route'])) {
			$this->_cache['blog_route'] = null;
			
			if ($this->isFullyIntegrated()) {
				$this->_cache['blog_route'] = $this->getStoreConfig('wordpress/integration/route');
			}
		}
		
		return $this->_cache['blog_route'];
	}

	/**
	 * Returns the pretty version of the blog route
	 *
	 * @return string
	 */
	public function getPrettyBlogRoute()
	{
		if ($route = $this->getBlogRoute()) {
			$route = str_replace('-', ' ', $route);
			return ucwords($route);
		}
	
		return null;	
	}

	/**
	 * Returns true if Magento/Wordpress are installed in different DB
	 * This can be configured in the Magento admin
	 *
	 * @return bool
	 */	
	public function isSeparateDatabase()
	{
		if (!isset($this->_cache['is_separate_db'])) {
			$this->_cache['is_separate_db'] = $this->getStoreConfigFlag('wordpress/database/is_different_db');
		}
		
		return $this->_cache['is_separate_db'];
	}
	
	/**
	 * Returns true if Magento/Wordpress are installed in the same DB
	 * This can be configured in the Magento admin
	 *
	 * @return bool
	 */
	public function isSameDatabase()
	{
		return !$this->isSeparateDatabase();
	}
	
	/**
	  * Returns true if full integration is enabled
	  *
	  */
	public function isFullyIntegrated()
	{
		if (!isset($this->_cache['is_fully_integrated'])) {
			$this->_cache['is_fully_integrated'] = $this->getStoreConfigFlag('wordpress/integration/full');
		}
		
		return $this->_cache['is_fully_integrated'];
	}
	
	/**
	  * Returns true if semi-integration is enabled
	  * Function will always return the opposite of isFullyIntegrated()
	  *
	  */
	public function isSemiIntegrated()
	{
		return !$this->isFullyIntegrated();
	}
	
	/**
	  * Gets a Wordpress option based on it's name
	  *
	  * If the value isn't found in the cache, it is fetched and added
	  *
	  */
	public function getCachedWpOption($optionName, $default = null)
	{
		if (!isset($this->_cache['option'][$optionName])) {
			$this->_cache['option'][$optionName] = $this->getWpOption($optionName, $default);
		}

		return $this->_cache['option'][$optionName];
	}
	
	/**
	  * Gets a Wordpress option based on it's name
	  *
	  */
	public function getWpOption($optionName, $default = null)
	{
		try {
			$option = Mage::getModel('wordpress/option')->load($optionName, 'option_name');
			
			if ($option->getOptionValue()) {
				return $option->getOptionValue();
			}
		}
		catch (Exception $e) {
			$this->log($e->getMessage());
		}

		return $default;
	}
	
	/**
	  * Formats a Wordpress date string
	  *
	  */
	public function formatDate($date, $format = null, $f = false)
	{
		if ($format == null) {
			$format = $this->getDefaultDateFormat();
		}
		
		/**
		 * This allows you to translate month names rather than whole date strings
		 * eg. "March","Mars"
		 *
		 */
		$len = strlen($format);
		$out = '';
		
		for( $i = 0; $i < $len; $i++) {	
			$out .= $this->__(Mage::getModel('core/date')->date($format[$i], strtotime($date)));
		}
		
		return $out;
	}
	
	/**
	  * Formats a Wordpress date string
	  *
	  */
	public function formatTime($time, $format = null)
	{
		if ($format == null) {
			$format = $this->getDefaultTimeFormat();
		}
		
		return $this->formatDate($time, $format);
	}
	
	/**
	  * Return the default date formatting
	  *
	  */
	public function getDefaultDateFormat()
	{
		return $this->getCachedWpOption('date_format', 'F jS, Y');
	}
	
	/**
	  * Return the default time formatting
	  *
	  */
	public function getDefaultTimeFormat()
	{
		return $this->getCachedWpOption('time_format', 'g:ia');
	}
	
	/**
	  * Logs an error to the Wordpress error log
	  *
	  */
	public function log($message, $level = null, $file = 'wordpress.log')
	{
		if ($this->getStoreConfigFlag('wordpress/debug/log_enabled')) {
			if ($message = trim($message)) {
				return Mage::log($message, $level, $file, true);
			}
		}
	}
	
	/**
	 * Retrieve the local path to file cache path
	 *
	 * @return string
	 */
	public function getFileCachePath()
	{
		return Mage::getBaseDir('var') . DS . 'wordpress' . DS;
	}
	
	/**
	 * Returns true if the current Magento version is below 1.4
	 *
	 * @return bool
	 */
	public function isLegacyMagento()
	{
		return version_compare(Mage::getVersion(), '1.4.0.0', '<');
	}

	/**
	 * Determine whether the Magento is the Enterprise edition
	 *
	 * @return bool
	 */
	public function isEnterpriseMagento()
	{
		return is_file(Mage::getBaseDir('code') . DS . implode(DS, array('Enterprise', 'Enterprise', 'etc')) . DS . 'config.xml');
	}
	
	/**
	 * Shortcut to get Param
	 *
	 * @param string $field
	 * @param null|mixed $default
	 * @return mixed
	 */
	public function getParam($field, $default = null)
	{
		return Mage::app()->getRequest()->getParam($field, $default);
	}
	
	/**
	 * Retrieve the path for the WordPress installation
	 * The main use of this is to include the phpass class file for Customer Synchronisation
	 *
	 * @return string
	 */
	public function getWordPressPath()
	{
		$path = $this->getStoreConfig('wordpress/misc/path');

		if (!$path) {
			$mUrlParts = parse_url(Mage::getBaseUrl());
			$wUrlParts = parse_url($this->getBaseUrl());

			$basePath = Mage::getBaseDir();
			
			if (isset($mUrlParts['path']) && !empty($mUrlParts['path'])) {
				$basePath = substr($basePath, 0, -(strlen($mUrlParts['path'])-1));
			}
			

			$path = $basePath . $wUrlParts['path'];
		}
		
		return rtrim($path, DS) . DS;
	}
	
	/**
	 * Retrieve the WordPress domain by getting the URL and the path
	 *
	 * @return string
	 */
	public function getDomain()
	{	
		$url = parse_url($this->getStoreConfig('web/unsecure/base_url'));
		$host = $url['host'];
		
		if (strpos($host, 'www.') !== false) {
			$host = substr($host, 4);
		}
	
		return $host;
	}
	
	/**
	 * Determine whether integration is enabled
	 *
	 * @return bool
	 */
	public function integrationIsEnabled()
	{
		if (!isset($this->_cache['integration_is_enabled'])) {
			$read = Mage::helper('wordpress/db')->getWordpressRead();
			$select = $read->select()->from(Mage::helper('wordpress/db')->getTableName('posts'), 'ID')->limit(1);
				
			try {
				$read->fetchOne($select);
				$this->_cache['integration_is_enabled'] = true;
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
				$this->_cache['integration_is_enabled'] = false;
			}
		}
		
		return $this->_cache['integration_is_enabled'];
	}

	/**
	 * Determine whether to adminhtml is being used
	 * Used by events loaded before area set
	 *
	 * @return bool
	 */
	public function isAdminhtmlArea()
	{	
		if (!isset($this->_cache['is_adminhtml_area'])) {
			if (Mage::getDesign()->getArea() == 'adminhtml') {
				$this->_cache['is_adminhtml_area'] = true;
			}
			else {				
				$requestUri = strtolower(Mage::app()->getFrontController()->getRequest()->getRequestUri());
				$adminFrontName = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
		
				if (isset($adminFrontName[0])) {
					$this->_cache['is_adminhtml_area'] = (strpos($requestUri, '/index.php/' . $adminFrontName[0] . '/') !== false);
				}
				else {
					$this->_cache['is_adminhtml_area'] = false;
				}
			}
		}
		
		return $this->_cache['is_adminhtml_area'];
	}
	
	/**
	 * Retrieve the current store code if set in Adminhtml
	 *
	 * @return null|string
	 */
	protected function _getStoreForConfig()
	{
		if ($this->isAdminhtmlArea()) {
			if (!isset($this->_cache['current_store'])) {
				$requestUri = strtolower(Mage::app()->getFrontController()->getRequest()->getRequestUri());
				$this->_cache['current_store'] = false;
				
				$store = Mage::app()->getRequest()->getParam('store', null);
				$website = Mage::app()->getRequest()->getParam('website', null);
	
				if ($store) {
					$this->_cache['current_store'] = $store;
				}
				else if (preg_match("/\/store\/([a-z0-9\-\_]{1,})\//", $requestUri, $results)) {
					$this->_cache['current_store'] = $results[1];
				}
				else if ($website) {
					if (preg_match("/\/website\/([a-z0-9\-\_]{1,})\//", $requestUri, $results)) {
						if ($website = $this->getWebsite($results[1])) {
							if ($group = $website->getDefaultGroup()) {
								if ($store = $group->getDefaultStore()) {
									$this->_cache['current_store'] = $store->getCode();
								}
							}
						}
					}
				}
				else {
					$this->_cache['current_store'] = $this->getDefaultStore()->getCode();
				}
			}

			return $this->_cache['current_store'] ? $this->_cache['current_store'] : null;
		}

		return null;
	}
	
	/**
	 * Retrieve value from the config
	 * Auto set the store if needed
	 *
	 * @param string $key
	 * @param string|null $store
	 * @return string
	 */
	public function getStoreConfig($key, $store = null)
	{
		if (!isset($this->_cache['config_' .$key])) {
			$this->_cache['config_' .$key] = Mage::getStoreConfig($key, is_null($store) ? $this->_getStoreForConfig() : $store);
		}
		
		return $this->_cache['config_' .$key];
	}
	
	/**
	 * Retrieve value from the config
	 * Auto set the store if needed
	 *
	 * @param string $key
	 * @param string|null $store
	 * @return bool
	 */
	public function getStoreConfigFlag($key, $store = null)
	{
		return Mage::getStoreConfigFlag($key, is_null($store) ? $this->_getStoreForConfig() : $store);
	}
	
	
	/**
	 * Retrieve the default store
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getDefaultStore()
	{
		if ($website = $this->getWebsite()) {
			if ($group = $website->getDefaultGroup()) {
				if ($store = $group->getDefaultStore()) {
					return $store;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve the default website
	 *
	 * @return Mage_Core_Model_Website
	 */
	public function getWebsite($websiteCode = null)
	{
		$websites = Mage::getResourceModel('core/website_collection');
		
		if (!is_null($websiteCode)) {
			$websites->addFieldToFilter('code', $websiteCode);
		}
		else {
			$websites->addFieldToFilter('is_default', 1);
		}
			
		$websites->getSelect()->limit(1);

		$websites->load();
		
		if (count($websites) > 0) {
			return $websites->getFirstItem();
		}
		
		return false;
	}
}
