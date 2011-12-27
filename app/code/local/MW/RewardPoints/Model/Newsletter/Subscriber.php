<?php

class MW_RewardPoints_Model_Newsletter_Subscriber extends Mage_Core_Model_Abstract
{
    public function newletterSaveBefore($argv)
    {
    	$subscriber = $argv->getSubscriber();
    	if($subscriber->getCustomerId()){
    		$_customer = Mage::getModel('rewardpoints/customer')->load($subscriber->getCustomerId());
	    	if($subscriber->getId())
	    	{
	    		$old_subscriber = Mage::getModel('newsletter/subscriber')->load($subscriber->getId());
	    		if(($old_subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) && ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED))
	    		{
	    			$rewardpoints = Mage::getStoreConfig('rewardpoints/config/reward_point_for_registering_subscriber');
					if($rewardpoints){
						$_customer->addRewardPoint($rewardpoints);
						$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SIGNING_UP_NEWLETTER, 'amount'=>(int)$rewardpoints, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>'', 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
						$_customer->saveTransactionHistory($historyData);
					}
	    		}
	    	}else
	    	{
	    		if(($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED))
	    		{
	    			$rewardpoints = Mage::getStoreConfig('rewardpoints/config/reward_point_for_registering_subscriber');
					if($rewardpoints){
						$_customer->addRewardPoint($rewardpoints);
						$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SIGNING_UP_NEWLETTER, 'amount'=>(int)$rewardpoints, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>'', 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
						$_customer->saveTransactionHistory($historyData);
					}
	    		}
	    	}
    	}
    }
}
