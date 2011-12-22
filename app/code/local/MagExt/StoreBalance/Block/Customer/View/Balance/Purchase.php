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
 * Customer's balance purchase block
 * Child block of MagExt_StoreBalance_Block_Customer_View_Balance
 * 
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Customer_View_Balance_Purchase extends Mage_Catalog_Block_Product_Abstract 
{
    /**
     * Get product which allows buy Store Balance units
     * @return NULL|Mage_Catalog_Model_Product
     */
    public function getProductForBuying()
    {
        if (!($productId = Mage::getStoreConfig('magext_storebalance/storebalance_purchase/product')))
            return null;
        $product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId);
		if (!$product->getId() || !$product->isSaleable())
            return null;

        return $product;
    }
    
    public function showButton($type='')
    {
        if (!$type)
            $type = 'both';
        $config = Mage::getStoreConfig('magext_storebalance/storebalance_purchase/button');
        return $config == $type || $config == 'both';
    }
    
    public function getPurchaseUrl($product)
    {
        return $this->getAddToCartUrl($product, array('gotocheckout' => 1));
    }
}