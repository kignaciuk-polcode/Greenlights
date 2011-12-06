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

class Netzarbeiter_GroupsCatalog_Model_Catalog_Resource_Eav_Mysql4_Category_Flat
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
{
	/**
	 * I need to override this method here to filter out hidden categories from the top menu dropdown below the first level...
	 * I haven't found a cleaner access point
	 *
	 * @param Mage_Catalog_Model_Category|int $parentNode
	 * @param int $recursionLevel
	 * @param int $storeId
	 * @return array
	 */
	protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0)
	{
		$nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId);

		if (! Mage::helper('groupscatalog')->inAdmin() && Mage::helper('groupscatalog')->moduleActive())
		{
			foreach (array_keys($nodes) as $nodeId)
			{
				if (Mage::helper('groupscatalog')->isCategoryHidden($nodes[$nodeId]))
				{
					unset($nodes[$nodeId]);
				}
			}
		}

		return $nodes;
	}

	/**
	 * WHY has Mageto not implemented this method to keep the API between eav and flat
	 * resource models consistent...
	 *
	 * @param int $entityId
	 * @param string $attribute
	 * @param int $store
	 * @return mixed
	 */
	public function getAttributeRawValue($entityId, $attribute, $store)
	{
		return Mage::getResourceModel('catalog/category')->getAttributeRawValue($entityId, $attribute, $store);
	}
}