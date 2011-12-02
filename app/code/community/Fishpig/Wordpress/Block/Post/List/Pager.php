<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List_Pager extends Mage_Page_Block_Html_Pager 
{
	/**
	 * Return the URL for a certain page of the collection
	 *
	 * @return string
	 */
	public function getPagerUrl($params=array())
	{
		$helper = Mage::helper('wordpress');
		
		if ($baseUrl = $this->_getBaseUrl()) {
			$uri = str_replace($helper->getUrl(), '', $baseUrl);
			
			return $helper->getUrl($uri, $params);		
		}
		
		if ($helper->isBlogMagentoHomepage()) {
			return Mage::getSingleton('core/url')->getUrl('', array('_query' => $params));
		}
		
		return $helper->getUrl('', $params);;
	}

	/**
	 * Retrieve the base URL for the pager
	 *
	 * @return false|string
	 */
	protected function _getBaseUrl()
	{
		$keys = array('wordpress_category', 'wordpress_archive', array(Mage::helper('wordpress/search'), 'getResultsUrl'));
		
		foreach($keys as $key) {
			if (is_array($key)) {
				try {
					if ($url = $key[0]->$key[1]()) {
						return $url;
					}
				}
				catch (Exception $e) {}
			}
			else if ($object = Mage::registry($key)) {
				if ($url = $object->getUrl()) {
					return $url;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Gets the path info from the request object
	 *
	 * @return string
	 */
	protected function _getPathInfo()
	{
		return trim(Mage::app()->getRequest()->getPathInfo(), '/');;
	}
}
