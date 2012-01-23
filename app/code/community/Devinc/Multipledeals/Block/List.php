<?php
class Devinc_Multipledeals_Block_List extends Mage_Core_Block_Template
{
	public function getLoadedProductCollection()
    {
		$multipledeals_collection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>'3'))->setOrder('type', 'ASC')->setOrder('multipledeals_id', 'DESC');
		
		$multipledeals_product_id = array();
		$multipledeals_keys = array();
		$i = 0;  
		  
		foreach ($multipledeals_collection as $multipledeals) {      
			$multipledeals_product_id[] = $multipledeals->getProductId();   
			$multipledeals_keys[$multipledeals->getProductId()] = $i;
			$i++;       
		}	  
		  
		$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('name')
				->addAttributeToSelect('small_image')
				->addAttributeToSelect('price')
				->addAttributeToSelect('special_price')
				->addAttributeToSelect('status')
				->addAttributeToFilter('entity_id', array('in' => $multipledeals_product_id))
				->load();
		
		$productCollectionOrdered = array();
		
		foreach ($productCollection as $prod) {      
			$productCollectionOrdered[$multipledeals_keys[$prod->getId()]] = $prod;          
		}	  
		
		ksort($productCollectionOrdered);
				
        return $productCollectionOrdered;
    }
	
    public function getProductUrl($_product)
    {			
		return $_product->getProductUrl();
    }	

	public function getPrice($_product)
    {	
       	$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		

		$_taxHelper  = $this->helper('tax');

		$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
				
		$price = $_product->getPrice();
		$converted_price = Mage::helper('directory')->currencyConvert($price, $baseCurrencyCode, $currentCurrencyCode);	
		$price_tax = $_taxHelper->getPrice($_product, $converted_price, $_simplePricesTax);		
		
		return $currency_symbol.number_format($price_tax,2);
    }
	
    public function getSpecialPrice($_product)
    {	
       	$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		

		$_taxHelper  = $this->helper('tax');

		$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
				
		$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq' => 3))->addFieldToFilter('product_id', array('eq' => $_product->getId()))->getFirstItem();
		$special_price = $multipledeals->getDealPrice();
			
		$converted_special_price = Mage::helper('directory')->currencyConvert($special_price, $baseCurrencyCode, $currentCurrencyCode);
		$special_price_tax = $_taxHelper->getPrice($_product, $converted_special_price, $_simplePricesTax);		
		
		return $currency_symbol.number_format($special_price_tax,2);
    }	

    public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
	
    public function getDealQty($_product)
    {					
		$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq' => 3))->addFieldToFilter('product_id', array('eq' => $_product->getId()))->getFirstItem();
		$deal_qty = $multipledeals->getDealQty();
		
		return $deal_qty;
    }	
	
	public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
}