<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_EnvironmentReport extends Fishpig_Wordpress_Helper_Abstract
{
	protected $_items = array();
	
	public function getReport()
	{
		if (count($this->_items) == 0) {
			$this->_collectModuleConfigs();
			$this->_collectEnvironmentConfigs();
			$this->_collectionIntegrationTestResults();
		}
		
		return $this->_items;
	}

	/**
	 * Collects the Magento environment data
	 */
	protected function _collectEnvironmentConfigs()
	{
		$configPaths = array('web/unsecure/base_url','web/seo/use_rewrites', 'design/package/name', 'design/theme/template', 'design/theme/layout');
		$configs = array();
		
		foreach($configPaths as $configPath) {
			$configs[$configPath] = Mage::getStoreConfig($configPath);
		}
		
		$configs['custom/magento/base_dir'] = Mage::getBaseDir();
		$configs['custom/magento/version'] = Mage::getVersion();
		$configs['custom/php/version'] = phpversion();

		return $this->addItem('Magento Configuration', $configs);
	}
	
	/**
	 * Collects the module config values
	 */
	protected function _collectModuleConfigs()
	{
		$configs = array_merge(Mage::getModel('adminhtml/config_data')->setSection('wordpress')->load(), Mage::getModel('adminhtml/config_data')->setSection('wordpress_blog')->load());
		
		if (isset($configs['wordpress/database/dbname'])) {
			$configs['wordpress/database/dbname'] = Mage::helper('core')->decrypt($configs['wordpress/database/dbname']);
		}
		
		foreach(array('wordpress/database/username', 'wordpress/database/password') as $path) {
			if (isset($configs[$path])) {
				if (Mage::helper('core')->decrypt($configs[$path])) {
					$configs[$path] = '****';
				}
				else {
					$configs[$path] = 'null';
				}
			}
		}

		$configs['wordpress/module/version'] = $this->getModuleVersion();
		
		return $this->addItem('Module Configuration', $configs);
	}
	
	/**
	 * Retrieve the version of the WordPress Integration module
	 *
	 * @return null|string
	 */
	public function getModuleVersion()
	{
		$modules = (array) Mage::getConfig()->getNode('modules')->children();

		if (isset($modules['Fishpig_Wordpress'])) {
			$module = (array)$modules['Fishpig_Wordpress'];
			return $module['version'];
		}
		
		return null;
	}
	/**
	 * Collects the results from the integration tests
	 */
	protected function _collectionIntegrationTestResults()
	{
		$results = Mage::helper('wordpress/debug_test')->performIntegrationTests();
		$configs = array();
		
		foreach($results as $result) {
			$configs[$result->getTitle()] = $result->getResponse();
		}
	
		return $this->addItem('Integration Results', $configs);	
	}
	
	/**
	 * Adds a collection of items to the $_items array
	 *
	 * @param string $section
	 * @param array $values
	 */
	public function addItem($section, array $values)
	{
		$this->_items[$section] = $values;
		return $this;
	}
	
	/**
	 * Retrieve a list of the last log entries
	 *
	 * @param int $entryLimit
	 * @return string|null
	 */
	public function getLogEntries($entryLimit = 100)
	{
		$logFile = Mage::getBaseDir('var') . '/log/wordpress.log';
		
		if (file_exists($logFile)) {
			try {
				$logs = explode("\n", file_get_contents($logFile));
				$entries = count($logs);
				
				if ($entries > $entryLimit) {
					$logs = array_splice($logs, (-($entryLimit)));
				}
				
				return implode("\n", array_reverse($logs));
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
			}
		}
		
		return false;
	}
}
