<?php

class Ebizmarts_Mailchimp_Block_Newsletter_Subscribe extends Mage_Newsletter_Block_Subscribe {

	public function canShowSmallBox(){

		$store = Mage::app()->getStore()->getStoreId();
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$email = ($customer->getEmail())? $customer->getEmail() : '' ;
		$helper = Mage::helper('mailchimp');

		if($helper->mailChimpEnabled($store) && !$helper->isSubscribed($email)){
				return true;
		}
        return false;
    }

	public function canShowMediumBox(){

    	$email = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getEmail();
		if($this->canShowSmallBox() && !Mage::helper('mailchimp')->isSubscribed($email)){
				return true;
		}
        return false;
    }

	public function isForcedToSubscribeCheckout(){

		$store = Mage::app()->getStore()->getStoreId();
		$helper = Mage::helper('mailchimp');

		if((bool)$helper->getSubscribeConfig('forece_checkout',$store)){
			return true;
		}
        return false;
    }

	public function getFormActionUrl(){

    	if(!parent::getFormActionUrl()){
    		return $this->getUrl('newsletter/subscriber/new', array('_secure' => true));
    	}
		return parent::getFormActionUrl();
    }

}
?>
