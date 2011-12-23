<?php

class MW_RewardPoints_Model_Newcustomer extends Mage_Customer_Model_Customer
{
	protected function _getRewardPointModel()
	{
		return Mage::getModel('rewardpoints/customer')->load($this->getId());
	}
	public function getRewardPoint()
	{
		$customer = $this->_getRewardPointModel();
		return $customer->getMwRewardPoint();
	}
	
	public function getMwRewardPoint()
	{
		return $this->getRewardPoint();
	}
	public function addRewardPoint($point)
	{
		$customer = $this->_getRewardPointModel();
		$customer->setMwRewardPoint($customer->getMwRewardPoint()+ $point);
		$customer->save();
	}
	
	public function getFriend()
	{
		$customer = $this->_getRewardPointModel();
		if($customer->getMwFriendId())
			return Mage::getModel('customer/customer')->load($customer->getMwFriendId());
		return false;
	}
	
	public function saveTransactionHistory($data = array())
	{
		$customer = $this->_getRewardPointModel();
		$customer->saveTransactionHistory($data);
	}
}