<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @copyright  Copyright (c) 2011 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Observer for the groups catalog extension. Remove hidden items from the collections
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_GroupsCatalog_Model_Observer extends Mage_Core_Model_Abstract
{
	/*
	 * Categories
	 */

	/**
	 * Add the hide groups property to category collections when the flat catalog is enabled
	 *
	 * @param Varien_Event_Observer $observer
	 * @return null
	 */
	public function coreCollectionAbstractLoadBefore($observer)
	{
		if (! Mage::helper('groupscatalog')->moduleActive() || $this->_isApiRequest()) return;
		$collection = $observer->getEvent()->getCollection();
		if (
			$collection instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
		)
		{
			//Mage::log(__METHOD__ . ' with ' . get_class($collection));
			$collection->addAttributeToSelect('groupscatalog_hide_group');
		}
	}

	/**
	 * Add the hide groups property to category collections when the flat catalog is disabled
	 *
	 * @param Varien_Event_Observer $observer
	 * @return null
	 */
	public function catalogCategoryCollectionLoadBefore($observer)
	{
		if (! Mage::helper('groupscatalog')->moduleActive() || $this->_isApiRequest()) return;
		$collection = $observer->getEvent()->getCategoryCollection();
		//Mage::log(__METHOD__ . ' with ' . get_class($collection));
		$collection->addAttributeToSelect('groupscatalog_hide_group');
	}

	/**
	 * Remove hidden caegories from the collection
	 *
	 * Since Mageto 1.3.1 this is also used for flat category collections
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function catalogCategoryCollectionLoadAfter($observer)
	{
		if (! Mage::helper('groupscatalog')->moduleActive() || $this->_isApiRequest()) return;
		$collection = $observer->getEvent()->getCategoryCollection();
		//Mage::log(__METHOD__ . ' with ' . get_class($collection));
		Mage::helper('groupscatalog')->removeHiddenCategories($collection);
	}

	/*
	 * Products
	 */

	 public function catalogProductCollectionLoadBefore($observer)
	 {
		if (! Mage::helper('groupscatalog')->moduleActive() || $this->_isApiRequest()) return;
		$collection = $observer->getEvent()->getCollection();
		//Mage::log(__METHOD__ . ' width ' . get_class($collection));
		$collection->addAttributeToSelect('groupscatalog_hide_group');
	 }

	/**
	 * Remove hidden products from the collection
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function catalogProductCollectionLoadAfter($observer)
	{
		if (! Mage::helper('groupscatalog')->moduleActive() || $this->_isApiRequest()) return;
        $collection = $observer->getEvent()->getCollection();
		//Mage::log(__METHOD__ . ' with ' . get_class($collection));
		Mage::helper('groupscatalog')->removeHiddenProducts($collection);
	}

	/*
	 * Misc
	 */

	/**
	 * Save the product visibility settings
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function adminhtmlCustomerSaveAfter($observer)
	{
		//Mage::helper('groupscatalog')->log(__METHOD__);
		if (! Mage::helper('groupscatalog')->moduleActive() || $this->_isApiRequest()) return;
		$visibleProducts = explode(',', Mage::app()->getRequest()->getPost('visible_products', ''));
		$customer = Mage::registry('current_customer');
		if ($visibleProducts && $customer && $customer->getId())
		{
			$product = Mage::getModel('catalog/product');
			foreach ($visibleProducts as $productState)
			{
				@list($productId, $isAccessible) = explode(':', $productState);
				$product->getResource()->load($product, $productId);
				if ($product->getId())
				{
					Mage::helper('groupscatalog')->setProductAccessibilityForGroup($product, $customer->getGroupId(), (bool) $isAccessible);
				}
			}
		}
	}

	/**
	 * Since the attribute backend model isn't used during mass updates, we need to implode() the array here.
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerActionPredispatchAdminhtmlCatalogProductActionAttributeSave($observer)
	{
		$controller = $observer->getEvent()->getControllerAction();
		$attributes = $controller->getRequest()->getParam('attributes', array());
		if (isset($attributes['groupscatalog_hide_group']) && is_array($attributes['groupscatalog_hide_group']))
		{
			$attributes['groupscatalog_hide_group'] = implode(',', $attributes['groupscatalog_hide_group']);
			$controller->getRequest()->setParam('attributes', $attributes);
		}
	}

	/**
	 * Return true if the reqest is made via the api
	 *
	 * @return boolean
	 */
	protected function _isApiRequest()
	{
		return Mage::app()->getRequest()->getModuleName() === 'api';
	}
}

