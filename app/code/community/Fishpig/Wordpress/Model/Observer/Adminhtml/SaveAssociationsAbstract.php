<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Observer_Adminhtml_SaveAssociationsAbstract
{
	protected function _getStoreId()
	{
		$storeId = (int)Mage::app()->getRequest()->getParam('store');
		
		if (!$storeId) {
			$store = Mage::helper('wordpress')->getCurrentFrontendStore();
			
			$storeId = $store->getId() ? $store->getId() : 1;
		}
		
		return $storeId;
	}
	
	/**
	 * Retrieve the resource class
	 *
	 */
	protected function _getResource()
	{
		return Mage::getSingleton('core/resource');
	}
}
