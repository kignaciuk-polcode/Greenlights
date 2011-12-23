<?php

class MW_RewardPoints_Model_Invitation extends Mage_Core_Model_Abstract
{
    public function referralLinkClick($argv)
    {
    	$invite = $argv->getInvite();
    	$referral_by = $argv->getReferralBy();
    	$request = $argv->getRequest();
    	$customer = Mage::getModel('customer/customer');
    	
    	switch ($referral_by){
    		case "1":
    			$customer->load($invite);
    			break;
    		case "2":
    			$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($invite);
    			break;
    	}
		//$customers->getSelect()->where("md5(email)='".$invite."'");
		
		if(Mage::helper('invitation')->rewardPointsEnabled())
		{
			if(method_exists($request,'getClientIp'))
				$clientIP = $request->getClientIp(true);
			else
			$clientIP = $request->getServer('REMOTE_ADDR');
			
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('transaction_detail',$clientIP)
			->addFieldToFilter('customer_id',$customer->getId())
			;
			
			if(!sizeof($transactions))
			{
				$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
				$points = Mage::getStoreConfig('rewardpoints/config/reward_point_for_invite_friend');
				if($points){
					$_customer->addRewardPoint($points);
					$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::INVITE_FRIEND, 'amount'=>$points, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$clientIP, 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					$_customer->saveTransactionHistory($historyData);
				}
			}
		}
		Mage::getModel('core/cookie')->set('friend', $customer->getId(), 3600*24);
    }
}