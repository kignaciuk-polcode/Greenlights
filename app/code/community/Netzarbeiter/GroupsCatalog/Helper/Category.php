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
 * Catalog category helper
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_GroupsCatalog_Helper_Category extends Mage_Catalog_Helper_Category
{
    /**
     * Check if a category can be shown
     *
     * @param  Mage_Catalog_Model_Category|int $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }
        
    	if (parent::canShow($category)) {
    		// if is hidden from customer group return false
    		return ! Mage::helper('groupscatalog')->isCategoryHidden($category);
    	}
        return false;
    }

	/**
	 * If the flat catalog is enabled there is no event that we can attach to :-/
	 * So we need to load the store categories as a collection and return the items array
	 * if expexted, that way the event that filters the categories is triggered.
	 *
	 * @param bool $sorted
	 * @param bool $asCollection
	 * @param bool $toLoad
	 * @return array
	 */
	public function getStoreCategories($sorted=false, $asCollection=false, $toLoad=true)
	{
		$collection = parent::getStoreCategories($sorted, $asCollection, $toLoad);

		if (! Mage::helper('catalog/category_flat')->isEnabled() || $asCollection)
		{
			return $collection;
		}

		/*
		 * If the flat catalog is enabled and the store categories are not loaded as a collection
		 * we need to filter the result.
		 */
		$result = array();
		foreach ($collection as $category)
		{
			if ($this->canShow($category))
			{
				$result[$category->getId()] = $category;
			}
		}
		return $result;
	}
	
}
