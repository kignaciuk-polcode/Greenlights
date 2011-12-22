<?php
/**
 * MagExtension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagExtension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magextension.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magextension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magextension.com for more information.
 *
 * @category   MagExt
 * @package    MagExt_StoreBalance
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */
 
/**
 * Main module helper
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('magext_storebalance/general/enable_storebalance');
	}
	
    public function isEnabledCoupons()
    {
        return Mage::getStoreConfigFlag('magext_storebalance/general/enable_storebalance_coupons');
    }
    
    public function getJsCurrency()
    {
        $websiteCollection = Mage::getSingleton('adminhtml/system_store')->getWebsiteCollection();
        $currencyList = array();
        foreach ($websiteCollection as $website)
        {
            $currencyList[$website->getId()] = $website->getBaseCurrencyCode();
        }
        return Zend_Json::encode($currencyList);
    }
    
    /**
     * 
     * 
     * @param $product Mage_Catalog_Model_Product
     */
    public function isAvailableProductCreate($product = NULL)
    {
        if (!$product) {
            $product = Mage::getModel('catalog/product');
        }
        $productCollection = $product->getCollection();
        /* @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $productCollection->addAttributeToFilter('store_balance_refill', 1)->load();
        return !(count($productCollection->getData()) && $product->getIdBySku('store_balance_refill'));
    }
}