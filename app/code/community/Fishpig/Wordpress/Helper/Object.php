<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Object extends Varien_Object
{
	/**
	 * Returns an instance of a helper based on the helper key
	 * 
	 * @param string $filter
	 * @return Fishpig_Wordpress_Helper_Filter_Abstract
	 */
	protected function _getHelperClass($helperKey)
	{
		$registryKey = '_helper/'.$helperKey;
		
		if ($helper = Mage::registry($registryKey)) {
			return $helper;
		}

		if ($helper = $this->_createHelperClass($helperKey)) {
			return $helper;
		}
		
		return null;
	}
	
	/**
	 * Returns an instance of a helper based on the helper key
	 * 
	 * @param string $filter
	 * @return Fishpig_Wordpress_Helper_Filter_Abstract
	 */
	protected function _createHelperClass($key)
	{
		$helperFile = Mage::getBaseDir('code') . DS . 'community' . DS . 'Fishpig' . DS . str_replace(' ', '/', ucwords(str_replace('/', ' ', str_replace('_', ' ', str_replace('/', '/Helper/', $key))))).'.php';
		$helperClass = 'Fishpig_'.str_replace(' ', '_', ucwords(str_replace(array('/', '_'), array(' Helper ', ' '), $key)));

		if (file_exists($helperFile)) {
			try {
				require_once($helperFile);
				$helperInstance = new $helperClass;
				Mage::register('_helper/'.$key, $helperInstance, true);
				return $helperInstance;
			}
			catch (Exception $e) {
				Mage::helper('wordpress')->log('Helper.Object->_createHelperClass: '.$e->getMessage());
				return false;
			}
		}
	}

	/**
	 * Convert an underscored string to a Class name
	 *
	 * @param string $str
	 * @return string
	 */
	public function camelize($str)
	{
		return trim('_'.strtolower(substr($str, 0, 1)).substr($this->_camelize($str), 1), '-');
	}

	/**
	 * Wrapper for preg_match that adds extra functionality
	 *
	 * @param string $pattern
	 * @param string $value
	 * @param int $keyToReturn
	 * @return mixed
	 */
	public function _match($pattern, $value, $keyToReturn = -1)
	{
		$result = array();
		preg_match($pattern, $value, $result);
		
		if ($keyToReturn == -1) {
			return $result;
		}
		else if (isset($result[$keyToReturn])) {
			return $result[$keyToReturn];
		}
		
		return null;
	}
	
	/**
	 * Wrapper for the Wordpress logger
	 *
	 * @param string $msg
	 */
	public function log($msg)
	{
		return Mage::helper('wordpress')->log($msg);
	}
}
