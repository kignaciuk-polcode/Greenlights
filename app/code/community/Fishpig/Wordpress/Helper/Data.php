<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Data extends Fishpig_Wordpress_Helper_Abstract
{
	
	public function getTopLinkUrl()
	{
		try {
			if ($this->isFullyIntegrated()) {
				if ($this->isBlogMagentoHomepage()) {
					return Mage::getUrl();
				}
				
				return $this->getUrl();
			}
		
			return $this->getCachedWpOption('home');
		}
		catch (Exception $e) {
			$this->log('Magento & WordPress are not correctly integrated (see entry below).');
			$this->log($e->getMessage());
		}
	}
	
	/**
	 * Returns the given string prefixed with the Wordpress table prefix
	 *
	 * @return string
	 */
	public function getTableName($table)
	{
		return Mage::helper('wordpress/db')->getTableName($table);
	}
	
	/**
	 * Determine whether the module is enabled
	 * This can be changed by going to System > Configuration > Advanced
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return !$this->getStoreConfigFlag('advanced/modules_disable_output/Fishpig_Wordpress');
	}
	
	/**
	 * Determine whether a WordPress plugin is enabled in the WP admin
	 *
	 * @param string $name
	 * @param bool $format
	 * @return bool
	 */
	public function isPluginEnabled($name, $format = true)
	{
		$name = $format ? Mage::getSingleton('catalog/product_url')->formatUrlKey($name) : $name;
		
		if ($plugins = $this->getCachedWpOption('active_plugins')) {
			$plugins = unserialize($plugins);

			foreach($plugins as $plugin) {
				if (strpos($plugin, $name) !== false) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Determine whether customer synch is enabled in the admin
	 *
	 * @return bool
	 */
	public function isCustomerSynchronisationEnabled()
	{
		return $this->getStoreConfigFlag('wordpress/customer_synchronisation/enabled');
	}

	/**
	 * Determine whether Cryllic locale support is enabled
	 *
	 * @return bool
	 */
	public function isCryllicLocaleEnabled()
	{
		return Mage::getStoreConfigFlag('wordpress_blog/locale/cyrillic_enabled');
	}

	/**
	 * Determine whether the blog is set to be the Magento homepage
	 *
	 * @return bool
	 */
	public function isBlogMagentoHomepage()
	{
		return Mage::getStoreConfigFlag('wordpress_blog/layout/blog_as_homepage')
			&& (Mage::getStoreConfig('web/default/front') == 'wordpress/homepage/index');
	}
}
