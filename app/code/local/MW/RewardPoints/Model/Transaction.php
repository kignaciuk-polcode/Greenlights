<?php
class MW_RewardPoints_Model_Transaction extends Varien_Object
{
	public function update($argv)
	{
		$customer = Mage::getModel('rewardpoints/customer')->load($argv->getModel()->getId());
		$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customer->getId())
					->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC')
		;
		//because select by current customer so have no record
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				case MW_RewardPoints_Model_Type::SUBMIT_PRODUCT_REVIEW:
					$reviewId = $transaction->getTransactionDetail();
					$review = Mage::getModel('review/review')->load($reviewId);
					$status = $transaction->getStatus();
					if($review->getId())
					{
						if($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED)
						{
							$status = MW_RewardPoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
							$customer->addRewardPoint($transaction->getAmount());
						}else if($review->getStatusId() == Mage_Review_Model_Review::STATUS_NOT_APPROVED)
						{
							$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
						}
					}else{
						$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
						$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					}
					
					$transaction->setStatus($status)->save();
					break;
					
					
				case MW_RewardPoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					$order = Mage::getModel('sales/order')->load($detail[1]);
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$status = MW_RewardPoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
							$customer->addRewardPoint($transaction->getAmount());
						}
					}else {
						$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
						$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					}
					
					$transaction->setStatus($status)->save();
					break;
					
					
				case MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					$order = Mage::getModel('sales/order')->load($detail[1]);
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$customer->addRewardPoint($transaction->getAmount());
							$status = MW_RewardPoints_Model_Status::COMPLETE;
							$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
						}
					}else {
						$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					}
					$transaction->setStatus($status)->save();
					break;
					
					
				case MW_RewardPoints_Model_Type::SEND_TO_FRIEND:
					//if the time is expired add reward points back to customer
					$oldtime =strtotime($transaction->getTransactionTime());
					$currentTime = strtotime(now());
					$hour = ($currentTime - $oldtime)/(60*60);
					$hourConfig = Mage::getStoreConfig('rewardpoints/send_reward_points/time_life');
					if($hourConfig && ($hour > $hourConfig))
					{
						$customer->addRewardPoint($transaction->getAmount());
						$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SEND_TO_FRIEND_EXPRIED, 'amount'=>(int)$transaction->getAmount(), 'balance'=>$customer->getMwRewardPoint(), 'transaction_detail'=>$transaction->getData('transaction_detail'), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
	           			$customer->saveTransactionHistory($historyData);
						$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE);
						$transaction->save();
					}
					break;
					
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$status = MW_RewardPoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now());
						}
					}else {
						$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
						$customer->addRewardPoint($transaction->getAmount());
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					}
					$transaction->setStatus($status)->save();
					break;
			
			
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$customer->addRewardPoint($transaction->getAmount());
							$status = MW_RewardPoints_Model_Status::COMPLETE;
							$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
						}
					}else {
						$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					}
					
					$transaction->setStatus($status)->save();
					break;
			}
		}
		
		
		
		$_transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('transaction_detail',$customer->getCustomerModel()->getEmail())
					->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::SEND_TO_FRIEND)
					->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
		;

		if(sizeof($_transactions)) foreach($_transactions as $_transaction)
		{
			$customer->addRewardPoint($_transaction->getAmount());
			$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::RECIVE_FROM_FRIEND, 'amount'=>$_transaction->getAmount(), 'transaction_detail'=>$_transaction->getCustomerId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
		    $customer->saveTransactionHistory($historyData);
		    $_transaction->setStatus(MW_RewardPoints_Model_Status::COMPLETE)->setTransactionDetail($customer->getCustomerId())->save();
		}
	}
}