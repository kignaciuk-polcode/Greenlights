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

class Netzarbeiter_GroupsCatalog_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * The attribute code used for category and product customer groupaccess checking
	 *
	 * @var string
	 */
	protected $_attributeCode = 'groupscatalog_hide_group';

	/**
	 * Cache the store category access groups
	 *
	 * @var array
	 */
	protected $_storeCategoryAccess = array();

	/**
	 * Cache the store product access groups
	 *
	 * @var array
	 */
	protected $_storeProductAccess = array();

	/**
	 * The value of the catalog/product attribute when the global config settings for customer group access should be used
	 */
	const USE_DEFAULT = '-2';

	/**
	 * The value of the catalog/product attribute when no customer group access restrictions should be enforced
	 */
	const NONE = '-1';

	/**
	 * Returns the attribute code used for category and product customer groupaccess checking
	 *
	 * @return string
	 */
	public function getAttributeCode()
	{
		return $this->_attributeCode;
	}

	/**
	 * Return the config value for the passed key
	 */
	public function getConfig($key)
	{
		$path = 'catalog/groupscatalog/' . $key;
		return Mage::getStoreConfig($path, Mage::app()->getStore());
	}

	/**
	 * Check if the script is called from the adminhtml interface
	 *
	 * @return boolean
	 */
	public function inAdmin()
	{
		return Mage::app()->getStore()->isAdmin();
	}

	/**
	 * Check if the extension has been disabled in the system configuration
	 *
	 * @return boolean
	 */
	public function moduleActive()
	{
		return (! (bool) $this->getConfig('disable_ext')) && ! $this->isApiRequest();
	}

	/**
	 * Return true if the reqest is made via the api
	 *
	 * @return boolean
	 */
	public function isApiRequest()
	{
		return Mage::app()->getRequest()->getModuleName() === 'api';
	}

	public function setProductAccessibilityForGroup(Mage_Catalog_Model_Product $product, $groupId, $isAccessible)
	{
		$productHidden = $this->isProductHidden($product, $groupId, false);
		if (
			($productHidden && ! $isAccessible) || (! $productHidden && $isAccessible)
			)
		{
			/*
			 * No change required
			 */
			return;
		}
		$productGroups = $this->getGroupsCatalogAttributeArray($product);

		if ($productGroups == array(self::USE_DEFAULT))
		{
			/*
			 * Replace USE_DEFAULT with an array containing every customer group
			 */
			$productGroups = $this->_convertDefaultSetting($product);
		}

		/*
		 * Add or remove group id from product array
		 */
		if (in_array($groupId, $productGroups))
		{
			/*
			 * Remove customer group id from product groups array
			 */
			$productGroups = $this->_removeValueFromArray($groupId, $productGroups);

			/*
			 * No empty array allowed - at least set self::NONE if no group may access the product
			 */
			if (! $productGroups) $productGroups = array(self::NONE);
		}
		else
		{
			/*
			 * Add customer group id to product group array
			 */
			$productGroups[] = $groupId;

			/*
			 * Since now at least one group may access the product, remove self::NONE if present
			 */
			if (in_array(self::NONE, $productGroups))
			{
				$productGroups = $this->_removeValueFromArray(self::NONE, $productGroups);
			}
		}
		$product->setData($this->getAttributeCode(), $productGroups)->save();
	}

	protected function _removeValueFromArray($removeValue, $array)
	{
		$newArray = array();
		foreach ($array as $i => $value)
		{
			if ($value != $removeValue) $newArray[] = $value;
		}
		return $newArray;
	}

	protected function _convertDefaultSetting()
	{
		$productGroups = $this->_getProductGroupArray();
		if (empty($productGroups))
		{
			/*
			 * Hide none
			 */
			$productGroups = array(self::NONE);
		}
		return $productGroups;
	}

	protected function _getAllCustomerGroupIds()
	{
		if (! isset($this->_allGroupIds))
		{
			$this->_allGroupIds = array();
			$collection = Mage::getModel('customer/group')->getCollection();
			foreach ($collection as $group)
			{
				$this->_allGroupIds[] = $group->getId();
			}
		}
		return $this->_allGroupIds;
	}

	/**
	 * Remove hidden products from a product collection
	 *
	 * @param Mage_Eav_Model_Entity_Collection_Abstrac $collection
	 */
	public function removeHiddenProducts(Mage_Eav_Model_Entity_Collection_Abstract $collection)
	{
		$store_access = $this->checkStoreProductAccess();
		$this->_removeHiddenCollectionItems($collection, $store_access);
	}

	/**
	 * Remove hidden categories from category collections
	 *
	 * @param Mage_Eav_Model_Entity_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract $collection
	 */
	public function removeHiddenCategories($collection)
	{
		$store_access = $this->checkStoreCategoryAccess();
		$this->_removeHiddenCollectionItems($collection, $store_access);
	}

	/**
	 * Mark hidden categories from category node array (required for flat catalog)
	 *
	 * @param array $categories
	 */
	public function hideHiddenCategoryNodes(array $categories)
	{
		$store_access = $this->checkStoreCategoryAccess();
        $access = array();
        foreach ($categories as $i => $category)
        {
            if ($this->_isItemHidden($category, $store_access)) {
                $category->setIsActive(false);
            }
        }
	}

	/**
	 * Remove hidden items from a product or category collection
	 *
	 * @param Mage_Eav_Model_Entity_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract $collection
	 * @param bool $store_access
	 */
	public function _removeHiddenCollectionItems($collection, $store_access)
	{
		foreach ($collection as $key => $item)
		{
			if ($this->_isItemHidden($item, $store_access))
			{
				$collection->removeItemByKey($key);
			}
		}
	}

	/**
	 * Checks if the given item or category is hidden
	 *
	 * @param Mage_Catalog_Model_Category | Mage_Catalog_Model_Product $item
	 * @param bool $store_access
	 * @param int $group_id
	 * @return boolean
	 */
	protected function _isItemHidden($item, $store_access, $group_id = null)
	{
		$hide_groups = $this->getGroupsCatalogAttributeArray($item);
		if (! isset($group_id)) $group_id = $this->getCustomerGroupId();
		if (count($hide_groups) == 1 && $hide_groups[0] == self::USE_DEFAULT)
			return ! $store_access;
		return ! $this->_checkGroupAccess($hide_groups, $group_id);
	}

	/**
	 * Return an array wiith the group id's from which to hide the given item.
	 *
	 * @param Mage_Catalog_Model_Category | Mage_Catalog_Model_Product $item
	 * @return array
	 */
	public function getGroupsCatalogAttributeArray($item)
	{
		$groups = $item->getDataUsingMethod($this->getAttributeCode());
		if (! isset($groups) || $groups === '')
		{
			/*
			 * Try to load that attribute in case it's just not loaded
			 */
			if ($item instanceof Mage_Catalog_Model_Category || $item instanceof Mage_Catalog_Model_Product)
			{
				$groups = $this->_loadAttributeValueOnItem($item);
			}
		}
		/*
		 *  if it really isn't set fall back to use store default
		 */
		if (! isset($groups) || $groups === '') return array(self::USE_DEFAULT);
		if (is_string($groups)) return explode(',', $groups);
		return $groups;
	}

	/**
	 * Return if the flat catalog for the specifed item type is active
	 *
	 * @param Mage_Catalog_Model_Category $item
	 * @return bool
	 */
	public function isFlatCatalogEnabled($item)
	{
		if ($item instanceof Mage_Catalog_Model_Category)
		{
			return Mage::helper('catalog/category_flat')->isEnabled();
		}

		return Mage::helper('catalog/product_flat')->isEnabled();
	}


	/**
	 * Load the value of the hide_groups attribute, set the attribute on the item,
	 * then return the loaded attribute value.
	 *
	 * @param Mage_Catalog_Model_Abstract $item
	 * @return mixed
	 */
	protected function _loadAttributeValueOnItem($item)
	{
		$result = array();
		if ($item->getId() && ($resource = $item->getResource()))
		{
			$value = $resource->getAttributeRawValue($item->getId(), $this->getAttributeCode(), Mage::app()->getStore()->getId());
			if (false === $value) {
				$value = self::USE_DEFAULT;
			}
			$result = explode(',', $value);
			$item->setDataUsingMethod($this->getAttributeCode(), $result);
		}
		return $result;
	}

	/**
	 * Check wether to hide the given product
	 *
	 * @param Mage_Catalog_Model_Product $item
	 * @param int $group_id
	 * @param bool $in_admin
	 * @return bool
	 */
	public function isProductHidden(Mage_Catalog_Model_Product $item, $group_id = null, $in_admin = null)
	{
		if (! isset($in_admin)) $in_admin = $this->inAdmin();
		if (! $this->moduleActive() || $in_admin) return false;

		$store_access = $this->checkStoreProductAccess($group_id);
		return $this->_isItemHidden($item, $store_access, $group_id);
	}

	/**
	 * Check wether to hide the given category
	 *
	 * @param Mage_Catalog_Model_Category $item
	 * @param int $group_id
	 * @param bool $in_admin
	 * @return bool
	 */
	public function isCategoryHidden($item, $group_id = null, $in_admin = null)
	{
		if (! isset($in_admin)) $in_admin = $this->inAdmin();
		if (! $this->moduleActive() || $in_admin) return false;

		$store_access = $this->checkStoreCategoryAccess($group_id);
		return $this->_isItemHidden($item, $store_access, $group_id);
	}

	/**
	 * Check if the store config setting allows the customer to view products
	 *
	 * @param int $group_id
	 * @return boolean
	 */
	public function checkStoreProductAccess($group_id = null) {
		if (! isset($group_id)) $group_id = $this->getCustomerGroupId();
		if (! isset($this->_storeProductAccess[$group_id]))
		{
			$this->_storeProductAccess[$group_id] = $this->_checkGroupAccess($this->_getProductGroupArray(), $group_id);
		}
		return $this->_storeProductAccess[$group_id];
	}

	/**
	 * Return the groups to hide products from from the store configuration
	 *
	 * @return array
	 */
	protected function _getProductGroupArray()
	{
		return $this->_getStoreGroupAccessArray('default_product_groups');
	}

	/**
	 * Check if the store config setting allows the customer to view categories
	 *
	 * @param int $group_id
	 * @return boolean
	 */
	public function checkStoreCategoryAccess($group_id = null) {
		if (! isset($group_id)) $group_id = $this->getCustomerGroupId();
		if (! isset($this->_storeCategoryAccess[$group_id]))
		{
			$this->_storeCategoryAccess[$group_id] = $this->_checkGroupAccess($this->_getCategoryGroupArray(), $group_id);
		}
		return $this->_storeCategoryAccess[$group_id];
	}

	/**
	 * Return the groups to hide categories from from the store configuration
	 *
	 * @return array
	 */
	protected function _getCategoryGroupArray()
	{
		return $this->_getStoreGroupAccessArray('default_category_groups');
	}

	/**
	 * Get the customer group access array for the specifie config key
	 *
	 * @param string $key
	 * @return array
	 */
	protected function _getStoreGroupAccessArray($key)
	{
		return explode(',', $this->getConfig($key));
	}

	/**
	 * Check if the customer group id is in the specified array
	 *
	 * @param array $groups
	 * @param int $group_id
	 * @return boolean
	 */
	protected function _checkGroupAccess($groups, $group_id)
	{
		return ! in_array($group_id, $groups);
	}

	/**
	 * Return the current customer group id. Logged out customers get the group id 0,
	 * not the default set in system > config > customers
	 *
	 * @return integer
	 */
	public function getCustomerGroupId()
	{
		$session = Mage::getSingleton('customer/session');
		if (! $session->isLoggedIn()) $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
		else $customerGroupId = $session->getCustomerGroupId();
		return $customerGroupId;
	}

	protected function _getProductAttributeModel($attributeCode)
	{
		if (! isset($this->_attributeModel))
		{
            $this->_attributeModel = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);
		}
		return $this->_attributeModel;
	}

	/**
	 * Add the product groups filter to a product collection
     * This is needed for the search collection
	 *
	 * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
	 */
	public function addGroupsFilterToProductCollection(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection, $customerGroupId = null)
	{
		return $this->addGroupsFilterToProductCollectionSelect($collection->getSelect(), $customerGroupId);
	}

	public function addGroupsFilterToProductCollectionSelect(Varien_Db_Select $select, $customerGroupId = null)
	{
		if ($this->moduleActive() && (! $this->inAdmin() || isset($customerGroupId)))
		{
			if (! isset($customerGroupId))
			{
				$customerGroupId = $this->getCustomerGroupId();
			}

            $attributeCode = $this->getAttributeCode();
            $attribute = $this->_getProductAttributeModel($attributeCode);

			if (! $attribute)
			{
				/*
				 * This seems to happen if no products exist in the catalog.
				 */
				return;
			}

            $tableAlias = '_' . $attributeCode . '_table';
            $attributeValueCol = 'IFNULL(' . $tableAlias . '.value' . ', ' . $attribute->getDefaultValue() . ')';

            $tableCondition = 'e.entity_id='.$tableAlias.'.entity_id AND ' .
                $tableAlias.'.attribute_id' . '=' .$attribute->getId();

            $select->joinLeft(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $tableCondition, 'value'
            );

            $default = Netzarbeiter_GroupsCatalog_Helper_Data::USE_DEFAULT;

            $commonConditionsSql = sprintf(
                    $attributeValueCol . " != '%1\$s' AND " .
                    "(" .
                        $attributeValueCol . " not like '%1\$s,%%' AND " .
                        $attributeValueCol . " not like '%%,%1\$s' AND " .
                        $attributeValueCol . " not like '%%,%1\$s,%%'" .
                    ")",
                    $customerGroupId
            );

            if ($this->checkStoreProductAccess($customerGroupId))
            {
                $select->where(
                    $attributeValueCol . " = ? OR ( " .
                    $commonConditionsSql . ")",
                    $default
                );
            }
            else
            {
                $select->where(
                    $attributeValueCol . " != ? AND ( " .
                    $commonConditionsSql . ")",
                    $default
                );
            }
		}
	}

	/**
	 * Dump a variable to the logfile (defaults to groupscatalog.log)
	 *
	 * @param mixed $var
	 * @param string $file
	 */
	public function log($var, $file = null)
	{
		$var = print_r($var, 1);

		$file = isset($file) ? $file : 'groupscatalog.log';

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $var = str_replace("\n", "\r\n", $var);
		Mage::log($var, null, $file);
	}
}

