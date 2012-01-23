<?php
require_once 'Mage/Checkout/controllers/MultishippingController.php';
class Devinc_Multipledeals_Checkout_MultishippingController extends Mage_Checkout_MultishippingController
{
	public function overviewAction()
    {
		Mage::getModel('multipledeals/multipledeals')->refreshCart();
		if (Mage::getSingleton('core/session')->getMultiShippingError()) {
			Mage::getSingleton('core/session')->setMultiShippingError(false);
			$this->_redirect('*/*/addresses');
			return $this;
		}
		
        parent::overviewAction();			
    }
	
	public function successAction()
    {
        $ids = $this->_getCheckout()->getOrderIds();
       
		foreach ($ids as $order_id) {		
			$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $order_id);
			
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
		}
		
		parent::successAction();
	}
	
}