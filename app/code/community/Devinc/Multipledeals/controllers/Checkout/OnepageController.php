<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Devinc_Multipledeals_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
	public function _expireAjax()
    {
		Mage::getModel('multipledeals/multipledeals')->refreshCart();
        parent::_expireAjax();			
    }
	
	public function successAction()
    {
		$session = $this->getOnepage()->getCheckout();
        $lastOrderId = $session->getLastOrderId();
       
	    $items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $lastOrderId);
		
		foreach ($items as $item) {			
			$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id',$item->getProductId())->addFieldToFilter('status', 3)->getFirstItem();
						
			$_product = Mage::getModel('catalog/product')->load($multipledeals->getProductId());
			$enabled = Mage::getStoreConfig('multipledeals/configuration/enabled');
			
			if ($multipledeals->getId()!='' && ($_product->getTypeId()=='simple' || $_product->getTypeId()=='virtual' || $_product->getTypeId()=='downloadable') && $enabled) {		
				$new_qty = $multipledeals->getDealQty()-$item->getQtyOrdered();
				
				$model = Mage::getModel('multipledeals/multipledeals');	
				
				$model->load($multipledeals->getId())
					  ->setDealQty($new_qty)		
					  ->save();		
				
				Mage::getModel('multipledeals/multipledeals')->refreshDeals();
			}
		}
		
		parent::successAction();
	}
	
}