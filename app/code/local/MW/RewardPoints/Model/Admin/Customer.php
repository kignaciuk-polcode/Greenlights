<?php

class MW_RewardPoints_Model_Admin_Customer extends Mage_Core_Model_Abstract
{
    public function saveRewardPoints($argv)
    {
    	$customer 	= $argv->getCustomer();
    	$request 	= $argv->getRequest();
    	
    	//Check invition information if exist add reward point to friend
        $friend_id = 0;
		$customer_id = $customer ->getId();
		$collection_customer = Mage::getModel('rewardpoints/customer')->getCollection()
										->addFieldToFilter('customer_id', $customer_id);
		if(sizeof($collection_customer) == 0){
			$_customer_table = Mage::getModel('rewardpoints/customer')->getCollection();
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	        $sql = 'INSERT INTO '.$_customer_table->getTable('customer').'(customer_id,mw_reward_point,mw_friend_id) VALUES('.$customer_id.',0,'. (($friend_id && Mage::helper('rewardpoints')->getInvitationModule())?$friend_id:0).')';
	        $write->query($sql);
		}
		
    	$_customer 	= Mage::getModel('rewardpoints/customer')->load($customer->getId());
    	$oldPoints 	= $_customer->getMwRewardPoint();
    	$amount 	= $request->getParam('reward_points_amount');
    	$action		= $request->getParam('reward_points_action');
    	$comment	= $request->getParam('reward_points_comment');
    	$newPoints 	= $oldPoints + $amount * $action;
    	
    	if($newPoints < 0) $newPoints = 0;
    	$amount = abs($newPoints - $oldPoints);
    	
    	if($amount > 0){
	    	$detail =$comment;
			$_customer->setData('mw_reward_point',$newPoints);
	    	$_customer->save();
	    	$balance = $_customer->getMwRewardPoint();
	    	$historyData = array('type_of_transaction'=>($action>0)?MW_RewardPoints_Model_Type::ADMIN_ADDITION:MW_RewardPoints_Model_Type::ADMIN_SUBTRACT, 'amount'=>$amount, 'balance'=>$balance, 'transaction_detail'=>$detail, 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
	    	$_customer->saveTransactionHistory($historyData);
    	}
    }
}