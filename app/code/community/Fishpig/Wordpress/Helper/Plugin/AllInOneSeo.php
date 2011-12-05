<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Plugin_AllInOneSeo extends Fishpig_Wordpress_Helper_Plugin_Abstract
{
	/**
	 * Determine whether All In One SEO is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('All In One SEO') && $this->getPluginOptions('aiosp_enabled') == '1';
	}
	
	/**
	 * Retrieve the meta fields
	 *
	 * @return array
	 */
	public function getMetaFields()
	{
		return array('title', 'description', 'keywords');
	}
	
	public function getPluginOptions($key = null)
	{
		if ($options = Mage::helper('wordpress')->getCachedWpOption('aioseop_options')) {
			$options = unserialize($options);
			
			if (!is_null($key)) {
				return isset($options[$key]) ? $options[$key] : null;
			}
			
			return $options;
		}
	}
	
	public function getPluginOption($key)
	{
		return $this->getPluginOptions('aiosp_'.$key);
	}
}
