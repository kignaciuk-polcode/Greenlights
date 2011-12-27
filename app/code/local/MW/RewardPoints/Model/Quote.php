<?php

class MW_RewardPoints_Model_Quote extends Mage_Core_Model_Abstract
{
    protected function _getSession()
    {
    	return Mage::getSingleton('checkout/session');
    }
    
    protected function _getCustomer()
    {
    	return Mage::getModel('rewardpoints/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }
    
	protected function _roundPoints($points,$up = true)
    {
		$config = Mage::getStoreConfig('rewardpoints/config/point_money_rate');
		$rate = explode("/",$config);
		$tmp = (int)($points/$rate[0]) * $rate[0];
		if($up)
			return $tmp<$points?$tmp+$rate[0]:$tmp;
		return $tmp;
    }
    
	public function collectTotalBefore($argv)
    {
    	if(Mage::helper('rewardpoints')->moduleEnabled())
		{
	    	$quote = $argv->getQuote();
	    	$address =$quote->isVirtual()?$quote->getBillingAddress():$quote->getShippingAddress();
			$subtotal = $address->getTotalAmount('subtotal')?$address->getTotalAmount('subtotal'):$quote->getSubtotal();
			$discount = $address->getTotalAmount('discount')?$address->getTotalAmount('discount'):$quote->getDiscount();
			$subtotal += $discount;
			//Convert subtotal to base currency
			$rate = Mage::helper('core')->currency(1,false);
			if($quote->getRewardpointDiscount() > $subtotal) 
			{
				$quote->setRewardpointDiscount($subtotal);
				$points = Mage::helper('rewardpoints')->exchangeMoneysToPoints($subtotal/$rate);
				$quote->setRewardpoint($this->_roundPoints($points,true))->save();
			}
		}
    }
}