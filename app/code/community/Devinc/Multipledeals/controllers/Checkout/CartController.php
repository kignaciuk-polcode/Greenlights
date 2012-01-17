<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Devinc_Multipledeals_Checkout_CartController extends Mage_Checkout_CartController
{
	public function indexAction()
    {
		Mage::getModel('multipledeals/multipledeals')->refreshCart();
	
        parent::indexAction();		
    }
	

   
}