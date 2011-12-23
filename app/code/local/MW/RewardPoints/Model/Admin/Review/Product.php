<?php
class MW_RewardPoints_Model_Admin_Review_Product
{
	public function save($argv)
	{
			$review = $argv->getObject();
			if(Mage::helper('rewardpoints')->moduleEnabled())
			{ 
					$transactions = Mage::getResourceModel('rewardpoints/rewardpointshistory_collection')
					->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::SUBMIT_PRODUCT_REVIEW)
					->addFieldToFilter('transaction_detail',$review->getId()."|".$review->getEntityPkValue())
					;
					if(!sizeof($transactions))
					{
	                   	$_customer = Mage::getModel('rewardpoints/customer')->load($review->getData('customer_id'));
	                    $points = Mage::getStoreConfig('rewardpoints/config/reward_point_for_submit_review');
						if($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED && $points)
	                    {
	                    	$status = MW_RewardPoints_Model_Status::COMPLETE;
	                    	$_customer->addRewardPoint($points);
	                    	$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SUBMIT_PRODUCT_REVIEW, 'amount'=>$points, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$review->getId()."|".$review->getEntityPkValue(), 'transaction_time'=>now(),'status'=>$status);
	                    	$_customer->saveTransactionHistory($historyData);
	                    }
					}
			}
	}
}