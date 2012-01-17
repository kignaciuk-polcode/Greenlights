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
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout observer model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Multipledeals_Model_Observer
{	
	public function refreshCart($observer) {		
		//$this->refreshDeals();
						
		if (Mage::getStoreConfig('multipledeals/configuration/enabled')) {			
			
			$quote = $observer->getEvent()->getCart()->getQuote();				

			$quote_items = $quote->getAllItems();

			if (count($quote_items)>0) {		
				$qtys = array();

				foreach ($quote_items as $quote) {
					$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id',$quote->getProductId())->addFieldToFilter('status', 3)->getFirstItem();
					$_product = Mage::getModel('catalog/product')->load($multipledeals->getProductId());
					if ($multipledeals->getId()!='' && ($_product->getTypeId()=='simple' || $_product->getTypeId()=='virtual' || $_product->getTypeId()=='downloadable')) {	
						if (!isset($qtys[$multipledeals->getProductId()])) {
							$qtys[$multipledeals->getProductId()] = 0;
						}
						$max_qty = $multipledeals->getDealQty();
						$qtys[$multipledeals->getProductId()] = $qtys[$multipledeals->getProductId()]+$quote->getQty();
						$product_name = $_product->getName();
						
						if ($max_qty<$qtys[$multipledeals->getProductId()]) {
							Mage::getSingleton('checkout/session')->getQuote()->setHasError(true);
							Mage::getSingleton('checkout/session')->addError('The maximum order qty available for the "'.$product_name.'" DEAL is '.$max_qty.'.');
							Mage::getSingleton('core/session')->setMultiShippingError(true);
						}							
					}       
					$i++;
				}					
			}
		}
	}
	
	public function updateDealQty($observer)
    {
		$items = $observer->getEvent()->getOrder()->getItemsCollection();
		
		foreach ($items as $item) {			
			$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id',$item->getProductId())->addFieldToFilter('status', 3)->getFirstItem();
						
			$_product = Mage::getModel('catalog/product')->load($multipledeals->getProductId());
			$enabled = Mage::getStoreConfig('multipledeals/configuration/enabled');
			
			if ($multipledeals->getId()!='' && ($_product->getTypeId()=='simple' || $_product->getTypeId()=='virtual' || $_product->getTypeId()=='downloadable') && $enabled) {		
				$new_qty = $multipledeals->getDealQty()-$item->getQtyOrdered();
				$new_sold_qty = $multipledeals->getQtySold()+$item->getQtyOrdered();				
				
				$model = Mage::getModel('multipledeals/multipledeals');	
				
				$model->load($multipledeals->getId())
					  ->setDealQty($new_qty)		
					  ->setQtySold($new_sold_qty)		
					  ->save();		
				
				Mage::getModel('multipledeals/multipledeals')->refreshDeals();
			}
		}
		
		return $this;
	}
	
	public function getFinalPrice($observer)
    {
    	$product = $observer->getEvent()->getProduct();
    	$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq' => 3))->addFieldToFilter('product_id', $product->getId())->getFirstItem();
		$enabled = Mage::getStoreConfig('multipledeals/configuration/enabled');
		
		if (!is_null($multipledeals->getId()) && $enabled) {
			$product_id = $multipledeals->getProductId();
		} else {
			$product_id = null;
		}
		
		if ($product->getTypeId()!='configurable') {
			if (!is_null($product_id) && $multipledeals->getDealQty()>0) {
				$product->setFinalPrice($multipledeals->getDealPrice());	
			}		
		} else {
			if (!is_null($product_id)) { 
				$product->setFinalPrice($multipledeals->getDealPrice());	
			}				
		}
	}
}
