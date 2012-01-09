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
	static protected $_cache = array();
	
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
				'_store' 	=> $this->getCurrentFrontendStore()->getCode(),
				'_secure' 	=> false,
				'_nosid' 	=> true,
			));
		}
		else {
			$url = $this->getHomeUrl() . '/' . ltrim($extra, '/');
		}
	
		return htmlspecialchars($url);
	}
	
	/**
	 * Retrieve the WordPress home URL
	 *
	 * @return string
	 */
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
		if (!$this->_isCached('blog_route')) {
			$this->_cache('blog_route', $this->isFullyIntegrated() ? $this->getCachedConfigValue('wordpress/integration/route') : null);
		}
		
		return $this->_cached('blog_route');
	}

	/**
	 * Returns true if Magento/Wordpress are installed in different DB
	 * This can be configured in the Magento admin
	 *
	 * @return bool
	 */	
	public function isSeparateDatabase()
	{
		if (!$this->_isCached('is_separate_db')) {
			$this->_cache('is_separate_db', $this->getCachedConfigValue('wordpress/database/is_different_db'));
		}
		
		return $this->_cached('is_separate_db');
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
		if (!$this->_isCached('is_fully_integrated')) {
			$this->_cache('is_fully_integrated', $this->getCachedConfigValue('wordpress/integration/full'));
		}
		
		return $this->_cached('is_fully_integrated');
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
		$cacheKey = 'option_' . $optionName;
		
		if (!$this->_isCached($cacheKey)) {
			$this->_cache($cacheKey, $this->getWpOption($optionName, $default));
		}

		return $this->_cached($cacheKey);
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
	  * Logs an error to the Wordpress error log
	  *
	  */
	public function log($message, $level = null, $file = 'wordpress.log')
	{
		if ($this->getCachedConfigValue('wordpress/debug/log_enabled')) {
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
		$path = $this->getCachedConfigValue('wordpress/misc/path');

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
		$url = parse_url($this->getCachedConfigValue('web/unsecure/base_url'));
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
		$helper = Mage::helper('wordpress/db');
		
		return $helper->isConnected() && $helper->isQueryable();
	}
	
	/**
	 * Retrieve a cached config value
	 *
	 * @param string $key
	 * @param string $website = null
	 * @param string $store = null
	 * @return mixed
	 */
	public function getCachedConfigValue($key, $website = null, $store = null)
	{
		if (!isset(self::$_cache['config_' . $key])) {
			self::$_cache['config_' . $key] = $this->getConfigValue($key, $website, $store, false);
		}
		
		return self::$_cache['config_' . $key];
	}
	
	/**
	 * If in Adminhtml area and $website & $store are null
	 * Auto set them
	 *
	 * @param string|null $website
	 * @param string|null $store
	 * @return array
	 */
	protected function _ensureWebsiteAndStore($website = null, $store = null)
	{
		if (Mage::getDesign()->getArea() == 'adminhtml' && is_null($website) && is_null($store)) {
			return array(
				Mage::app()->getRequest()->getParam('website', null), 
				Mage::app()->getRequest()->getParam('store', null),
			);
		}
		
		return array(
			Mage::app()->getStore()->getWebsite()->getCode(),
			Mage::app()->getStore()->getCode(),
		);
	}

	/**
	 * Retrieve a config value
	 *
	 * @param string $key
	 * @param string $website = null
	 * @param string $store = null
	 * @param bool $reload = true
	 * @return mixed
	 */	
	public function getConfigValue($key, $website = null, $store = null, $reload = true)
	{
		list($website, $store) = $this->_ensureWebsiteAndStore($website, $store);
		
		$section = substr($key, 0, strpos($key, '/'));
		
		$options = array(array(null, null));
		
		if ($website && $store) {
			$options[] = array($website, null);
			$options[] = array($website, $store);
		}
		else if ($website) {
			$options[] = array($website, null);
		}
		
		$options = array_reverse($options);

		foreach($options as $option) {
			if ($configData = $this->_getConfigDataForSection($section, $option[0], $option[1], $reload)) {
				if (isset($configData[$key])) {
					return $configData[$key];
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Retrieve the data for a config section
	 *
	 * @param string $section
	 * @param string $website = null
	 * @param string $store = null
	 * @param bool $reload = true
	 * @return mixed
	 */
	protected function _getConfigDataForSection($section, $website = null, $store = null, $reload = true)
	{
		$cacheKey = 'config_data_' . $section;
		
		if ($website) {
			$cacheKey .= '_w-' . $website;
		}
		
		if ($store) {
			$cacheKey .= '_s-' . $store;
		}

		if (!isset(self::$_cache[$cacheKey]) || $reload) {
			self::$_cache[$cacheKey] = array();
			
	        $configDataObject = Mage::getModel('adminhtml/config_data')
				->setSection($section)
				->setWebsite($website)
				->setStore($store);

        	self::$_cache[$cacheKey] = $configDataObject->load();				
		}
		
		return self::$_cache[$cacheKey];
	}
	
	/**
	 * Retrieve the current store code
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getCurrentFrontendStore()
	{
		if (!isset(self::$_cache['current_frontend_store'])) {
			$store = Mage::app()->getStore();
			
			if (!$store->getId() || $store->getCode() == 'admin') {
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$select = $connection->select()
					->from($this->getTableName('core/store'), 'store_id')
					->where('store_id > ?', 0)
					->where('code != ?', 'admin')
					->limit(1)
					->order('sort_order ASC');
				
				$store = Mage::getModel('core/store')->load($connection->fetchOne($select));

				self::$_cache['current_frontend_store'] = $store;
			}
			else {
				self::$_cache['current_frontend_store'] = $store;
			}
		}
		
		return self::$_cache['current_frontend_store'];
	}
	
	/**
	 * Store a value in the cache
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return $this;
	 */
	protected function _cache($key, $value)
	{
		self::$_cache[$key] = $value;
		
		return $this;
	}
	
	/**
	 * Determine whether there is a value in the cache for the key
	 *
	 * @param string $key
	 * @return bool
	 */
	protected function _isCached($key)
	{
		return isset(self::$_cache[$key]);
	}
	
	/**
	 * Retrieve a value from the cache
	 *
	 * @param string $key
	 * @param mixed $default = null
	 * @return mixed
	 */
	protected function _cached($key, $default = null)
	{
		if ($this->_isCached($key)) {
			return self::$_cache[$key];
		}
		
		return $default;
	}
}
