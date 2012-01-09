<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

/**
 * This may be a horrible way to do this but for a little longer, we have to support Magento 1.3.2.4
 *
 */
class Fishpig_Wordpress_Integration_Test_Results_Collection extends Varien_Data_Collection
{
	public function getSize()
	{
		return count($this->_items);
	}
}

class Fishpig_Wordpress_Helper_System extends Fishpig_Wordpress_Helper_Abstract
{

	/**
	 * Retrieve a collection of integration results
	 *
	 * @return Varien_Data_Collection
	 */
	public function getIntegrationTestResults()
	{
		if (!$this->_isCached('integration_results')) {
			$results = new Fishpig_Wordpress_Integration_Test_Results_Collection();
		
			$results->addItem($this->_isConnected());
			$results->addItem($this->_isQueryable());
			
			if (Mage::helper('wordpress')->isFullyIntegrated()) {
				$results->addItem($this->_hasValidWordPressUrls());
				$results->addItem($this->_hasValidBlogRoute());
				$results->addItem($this->_hasValidWordPressPath());
				
				if ($result = $this->_hasValidHomepageValues()) {
					$results->addItem($result);
				}
			}
			
			if ($result = $this->_isValidMagentoVersion()) {
				$results->addItem($result);
			}
		
			$this->_cache('integration_results', $results);
		}
		
		return $this->_cached('integration_results');
	}

	/**
	 * Determine whether the integration tests encountered errors
	 *
	 * @return bool
	 */
	public function integrationHasErrors()
	{
		if ($results = $this->getIntegrationTestResults()) {
			foreach($results as $result) {
				if ($result->getIsError()) {
					return true;
				}
			}
			
			return false;			
		}
		
		return true;
	}
	
	/**
	 * Determine whether the database is connected
	 *
	 * @return Varien_Object
	 */
	protected function _isConnected()
	{
		$title = 'Database Connection';

		if (!Mage::helper('wordpress/db')->isConnected()) {
			if ($host = $this->_getDatabaseHost()) {
				$response = $this->__("Unable to connect to database '%s' at host '%s'", $this->_getDatabaseName(), $host); 
			}
			else {
				$response = $this->__("You need to specify a host for your database server. You can find this value in wp-config.php"); 
			}
			
			return $this->_createTestResultObject($title, $response, false);
		}
		
		return $this->_createTestResultObject($title);
	}
	
	/**
	  * Determine whether the database is queryable
	  *
	  * @return Varien_Object
	  */
	protected function _isQueryable()
	{
		$title = 'Database Query';

		if (!Mage::helper('wordpress/db')->isQueryable()) {
			if ($prefix = Mage::helper('wordpress/db')->getTablePrefix()) {
				$response = $this->__("Unable to query the WordPress database using the table prefix '%s'. Ensure the details entered below match those in wp-config.php", $prefix); 
			}
			else {
				$response = $this->__("Unable to query the WordPress database using no table prefix. Ensure the details entered below match those in wp-config.php");
			}

			return $this->_createTestResultObject($title, $response, false);
		}
		
		return $this->_createTestResultObject($title);
	}
	
	/**
	 * Determine whether the Wordpress URL's are valid
	 *
	 * @return Varien_Object
	 */
	protected function _hasValidWordPressUrls()
	{
		$title = "WordPress URL's";
		$helper = Mage::helper('wordpress');
		
		$blogUrl 	 = rtrim(str_replace('/index.php', '', $helper->getUrl()), '/');
		$installUrl = rtrim($helper->getCachedWpOption('siteurl'), '/');
		
		if (!$installUrl) {
			$response = $this->__("Unable to determine your WordPress install URL");

			return $this->_createTestResultObject($title, $response, false);			
		}
		else if ($blogUrl == $installUrl) {
			$response = $this->__("Your blog URL (site address) matches your install URL (WordPress address). Change your blog route or move WordPress to a different sub-directory");

			return $this->_createTestResultObject($title, $response, false);		
		}
	
		return $this->_createTestResultObject($title);	
	}
	
	/**
	 * Determine whether the blog route is valid
	 *
	 * @return Varien_Object
	 */
	protected function _hasValidBlogRoute()
	{
		$title = "Blog Route";	
		$helper = Mage::helper('wordpress');
		
		$wpBlogUrl 	  = rtrim($helper->getCachedWpOption('home'), '/');
		$mageBlogUrl = rtrim(($helper->getUrl()), '/');
		
		if ($wpBlogUrl != $mageBlogUrl) {
			$response = $this->__("Go to the General Settings page of your WordPress Admin and set the 'Site address (URL)' field to '%s'", $mageBlogUrl);

			return $this->_createTestResultObject($title, $response, false);		
		}
	
		return $this->_createTestResultObject($title);		
	}
	
	/**
	 * Determine whether the WordPress path is valid
	 *
	 * @return Varien_Object
	 */
	protected function _hasValidWordPressPath()
	{
		$title = "WordPress Path";	

		$path = Mage::helper('wordpress')->getWordPressPath();
		
		if (!($path && is_dir($path) && is_file(rtrim($path, '/\\') . DS . 'wp-config.php'))) {
			$response = $this->__("Your WordPress path is incorrect. You must set the path to your WordPress installation");

			return $this->_createTestResultObject($title, $response, false);		
		}
	
		return $this->_createTestResultObject($title);	
	}
	
	/**
	 * Determine whether the homepage values are correctly set
	 *
	 * @return false|Varien_Object
	 */
	protected function _hasValidHomepageValues()
	{
		$title     	 = 'Homepage';
		$response = null;
		$helper 	 = Mage::helper('wordpress');
		
		$blogAsHome 	= $helper->getCachedConfigValue('wordpress_blog/layout/blog_as_homepage');
//		$front 				= $helper->getCachedConfigValue('web/default/front');
		$front 				= Mage::getStoreConfig('web/default/front');
		
		if ($blogAsHome) {
			if ($front != 'wordpress/homepage/index') {
				$response = "You have set your blog as your Mageto homepage. To complete this, you need to change Web > Default Pages > Default Web URL to 'wordpress/homepage/index'.";
			}
		}
		else if ($front == 'wordpress/homepage/index') {
			$response = "You no longer want your blog as your Magento homepage. You need to reset the value at Web > Default Pages > Default Web URL to 'cms'.";
		}
		else if (!$front) {
			$response = "The value at Web > Default Pages > Default Web URL is empty. This can lead to no homepage displaying in Magento. To display the standard Magento homepage, set this value to 'cms'";
		}
		
		if (!is_null($response)) {
			return $this->_createTestResultObject($title, $response, false);
		}
		
		return false;
	}
	
	/**
	 * Determine whether the Magento version is valid
	 *
	 * @return false|Varien_Object
	 */
	protected function _isValidMagentoVersion()
	{
		if (Mage::helper('wordpress')->isLegacyMagento()) {
			$response = 'From Q1 2012, Magento 1.3 will not be supported by this extension. To continue using this extension, please upgrade to Magento 1.4.0.0 or later';
			
			return $this->_createTestResultObject('Magento Version', $response, 'warning-msg');
		}
	
		return false;
	}
	
	/**
	 * Create a test result object
	 *
	 * @param string $title
	 * @param string $response = ': )'
	 * @param mixed $result = true
	 * @return Varien_Object
	 */
	protected function _createTestResultObject($title, $response = ': )', $result = true)
	{
		$resultClass = $result;
		
		$isError = ($result !== true);

		if ($result === true) {
			$resultClass = 'success-msg';
		}
		else if ($result === false) {
			$resultClass = 'error-msg';
		}
		
		return new Varien_Object(array(
			'title' => $this->__($title), 
			'response' => $response, 
			'is_error' => $isError, 
			'result' => $resultClass
		));
	}
	
	/**
	 * Retrieve the database host
	 *
	 * @return string
	 */
	protected function _getDatabaseHost()
	{
		$helper = Mage::helper('wordpress');
		
		if ($helper->isSeparateDatabase()) {
			return $helper->getCachedConfigValue('wordpress/database/host');
		}
		
		return '';
	}
	
	/**
	 * Retrieve the database name
	 *
	 * @return string
	 */
	protected function _getDatabaseName()
	{
		$helper = Mage::helper('wordpress');
		
		if ($helper->isSeparateDatabase()) {
			if ($dbname = $helper->getCachedConfigValue('wordpress/database/dbname')) {
				return Mage::helper('core')->decrypt($dbname);
			}
		}
		
		return '';
	}
	
	/**
	  * Determine whether the ACL is valid
	  *
	  * @return bool
	  */
	public function isAclValid()
	{
		try {
			$session = Mage::getSingleton('admin/session');
			$resourceId = $session->getData('acl')->get("admin/system/config/wordpress")->getResourceId();
			return $session->isAllowed($resourceId);	
		}
		catch (Exception $e) { }
		
		return false;
	}
}
