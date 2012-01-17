<?php

class Devinc_Multipledeals_Model_Multipledeals extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('multipledeals/multipledeals');
    }
	
	public function getIsMultipleDeal($product_id = 0)
    {		
		$this->refreshDeals();
        $multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', 3)->addFieldToFilter('product_id', $product_id)->getFirstItem();
		
		if ($multipledeals->getId()!='') {
			return true;
		} else {
			return false;
		}
	}
	
	public function refreshDeals() {
		$multipledeals_collection = Mage::getModel('multipledeals/multipledeals')->getCollection()->setOrder('multipledeals_id', 'DESC');
		$is_main_deal_set = false;
		
		foreach ($multipledeals_collection as $multipledeals) {		
			
			// get in stock value
			$_product = Mage::getModel('catalog/product')->load($multipledeals->getProductId());	
			$stockItem = $_product->getStockItem();
						
			if ($stockItem->getIsInStock()) {
				if ($_product->getTypeId()=='simple' || $_product->getTypeId()=='virtual' || $_product->getTypeId()=='downloadable') {
					if ($multipledeals->getDealQty()>0) {
						$in_stock = true;
					} else {
						$in_stock = false;						
					}
				} else {
					$in_stock = true;						
				}
			} else {
				$in_stock = false;
			}
				
			$product_status = $_product->getStatus();
			$current_date_time = Mage::getModel('core/date')->date('Y-m-d H,i,s');	
			if (!$in_stock || $product_status!=1) {
				$model = Mage::getModel('multipledeals/multipledeals');	
				$model->setId($multipledeals->getId())
					  ->setStatus('2')
					  ->setType('3')
					  ->save();
			} else {			
				if ($current_date_time>=($multipledeals->getDateFrom().' '.$multipledeals->getTimeFrom()) && $current_date_time<=($multipledeals->getDateTo().' '.$multipledeals->getTimeTo()) && $multipledeals->getStatus()!=2) 		     {
					if ($multipledeals->getType()==1 && !$is_main_deal_set) {
						$is_main_deal_set = true;
						$model = Mage::getModel('multipledeals/multipledeals');	
						$model->setId($multipledeals->getId())
							  ->setStatus('3')
							  ->setType('1')
							  ->save();
					} else {
						$model = Mage::getModel('multipledeals/multipledeals');	
						$model->setId($multipledeals->getId())
							  ->setStatus('3')
							  ->setType('2')
							  ->save();
					}
				} elseif ($current_date_time<=($multipledeals->getDateFrom().' '.$multipledeals->getTimeFrom()) && $multipledeals->getStatus()!=2) {
					$model = Mage::getModel('multipledeals/multipledeals');	
					$model->setId($multipledeals->getId())
						  ->setStatus('1')
						  ->setType('4')
						  ->save();
				} elseif ($current_date_time>=($multipledeals->getDateTo().' '.$multipledeals->getTimeTo()) && $multipledeals->getStatus()!=2) {
					$model = Mage::getModel('multipledeals/multipledeals');	
					$model->setId($multipledeals->getId())
						  ->setStatus('4')
						  ->setType('3')
						  ->save();
				} else {
					$model = Mage::getModel('multipledeals/multipledeals');	
					$model->setId($multipledeals->getId())
						  ->setStatus('2')
						  ->setType('3')
						  ->save();			
				}
			}

			if (($current_date_time>=($multipledeals->getDateTo().' '.$multipledeals->getTimeTo()) || $multipledeals->getStatus()==2) && $multipledeals->getDisable()==2 && $product_status==1) {
				$updateProduct = Mage::getModel('catalog/product')->setStoreId(0)->load($multipledeals->getProductId())->setStatus(2)->save();
			}
		}
		
		if (!$is_main_deal_set) {
			$main_deal_id = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq' => 3))->setOrder('multipledeals_id', 'DESC')->getFirstItem()->getId();
			if ($main_deal_id!='') {
				$model = Mage::getModel('multipledeals/multipledeals');	
				$model->setId($main_deal_id)
					  ->setType('1')
					  ->save();
			}
		}
	 
        return $this;
	}
	
	public function refreshCart() {		
		//$this->refreshDeals();
		if (Mage::getStoreConfig('multipledeals/configuration/enabled')) {			
			
			$quote_product_ids = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
			$product_ids = Array();
			
			foreach ($quote_product_ids as $item) {
				$product_ids[] = $item->getProductId();
			}	
			
			$quote_items_ids = Mage::getModel('checkout/cart')->getQuote()->getAllItems();
			
			$i = 0;
			$qtys = array();

			foreach ($quote_items_ids as $quote_id) {
				$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id',$product_ids[$i])->addFieldToFilter('status', 3)->getFirstItem();
				$_product = Mage::getModel('catalog/product')->load($multipledeals->getProductId());
				if ($multipledeals->getId()!='' && ($_product->getTypeId()=='simple' || $_product->getTypeId()=='virtual' || $_product->getTypeId()=='downloadable')) {	
					if (!isset($qtys[$multipledeals->getProductId()])) {
						$qtys[$multipledeals->getProductId()] = 0;
					}
					$max_qty = $multipledeals->getDealQty();
					$qtys[$multipledeals->getProductId()] = $qtys[$multipledeals->getProductId()]+$quote_id->getQty();
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