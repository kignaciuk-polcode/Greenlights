<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug extends Fishpig_Wordpress_Helper_Abstract
{

	public function performIntegrationTests()
	{
		return Mage::helper('wordpress/debug_test')->performTests();
	}


	
	public function getIntegrationTestClasses()
	{
		return array('connection');
		return array('connection', 'query', 'integration');
	}
	
	/**
	 * To go
	 */
	const _integration_error_route_mismatch_advice = 'Go To WordPress > Settings > Reading and enter %s in the field \'Site Address (URL)\''; 
	
	const _matching_wp_urls = 'Matching WP URLs';
	const _matching_blog_urls = 'Matching blog URLs';
	
	const _not_applicable = 'N/A';
	
	protected $_testResultsCollection = null;
	
	
	public function getTestResultsCollection()
	{
		if (is_null($this->_testResultsCollection)) {
			$collection = new Varien_Data_Collection();

			foreach($this->_getCheckMethods() as $method => $title) {
				$statusClass = 'success-msg';
				
				try {
					$response = $this->_performCheck($method);
				}
				catch (Exception $e) {
					$response = $e->getMessage();	
					$statusClass = 'error-msg';
				}
			
				$collection->addItem(
					new Varien_Object(array('title' => $title, 'response' => $response, 'result' => $statusClass))
				);
			}
			
			$this->_testResultsCollection = $collection;
		}
		
		return $this->_testResultsCollection;
	}

	
	protected function _performCheck($method)
	{
		return call_user_func(array($this, $method));
	}


	
	
	
	
	
	
	
	/**
	  * Test methods
	  *
	  */
	
	public function canConnect()
	{
		try {
			if (Mage::helper('wordpress/db')->isConnected(false) == false) {
				throw new Exception(self::_db_connection_error_advice);
			}
		}
		catch (Exception $e) {
			$this->log($e->getMessage());
			throw $e;
		}
	}
	
	public function canQuery()
	{
		if (Mage::helper('wordpress/db')->isConnected() == true) {
			try {
				if (Mage::helper('wordpress/db')->isQueryable(false)) {
					return;
				}
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
				throw $e;
			}
		}

		$this->_throwNotApplicableException();
	}

	public function isIntegrated()
	{
		if (Mage::helper('wordpress/db')->isConnected() == false) {
			throw new Exception('N/A');
		}
		
		if (Mage::helper('wordpress/db')->isQueryable() == false) {
			throw new Exception('N/A');
		}
		
		if ($this->isFullyIntegrated()) {
			return $this->_isFullyIntegrated();
		}
		
		return $this->_isSemiIntegrated();
	}
	
	protected function _isSemiIntegrated()
	{
		return true;
	}
	
	protected function _isFullyIntegrated()
	{
		$blogUrlMagento =  rtrim($this->getUrl(), '/');
		$blogUrlWp = rtrim($this->getCachedWpOption('siteurl'), '/');
		$wpUrl = trim($this->getCachedWpOption('home'), '/');

		if ($blogUrlWp == $wpUrl) {
			throw new Exception(self::_matching_wp_urls);
		}
		else if ($blogUrlMagento == $blogUrlWp) {
			throw new Exception(self::_matching_blog_urls);
		}
		
		return;
	}
	
	protected function _throwNotApplicableException()
	{
		throw new Exception(self::_not_applicable);
	}

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
