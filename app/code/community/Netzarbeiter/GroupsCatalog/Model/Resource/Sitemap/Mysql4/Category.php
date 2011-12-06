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
 * Extend sitemap resource catalog collection model to filter out hidden categories
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @copyright  Copyright (c) 2008 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Netzarbeiter_GroupsCatalog_Model_Resource_Sitemap_Mysql4_Category
	extends Mage_Sitemap_Model_Mysql4_Catalog_Category
{
    /**
     * Get category collection array and filter out hidden categories
     *
     * @return array
     */
    public function getCollection($storeId)
    {
    	$categories = parent::getCollection($storeId);
    	$helper = Mage::helper('groupscatalog');
		if ($helper->moduleActive())
		{
			$tmp = array();
			foreach ($categories as $item)
			{
				// $item is a Varien_Object, no descendant of Mage_Catalog_Model_Resource_Eav_Mysql4_Collection_Abstract
				$cat = Mage::getModel('catalog/category')->setId($item->getId());
				
				if (! $helper->isCategoryHidden($cat, Mage_Customer_Model_Group::NOT_LOGGED_IN_ID, $in_admin = false))
				{
					$tmp[$item->getId()] = $item;
				}
			}
			$categories = $tmp;
		}
		
		return $categories;
    }
}