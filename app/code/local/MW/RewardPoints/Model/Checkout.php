<?php

class MW_RewardPoints_Model_Checkout extends Mage_Core_Model_Abstract
{
    public function placeAfter($argv)
    {
    	if(Mage::helper('rewardpoints')->moduleEnabled())
		{
			$order = $argv->getOrder();
	    	$customer = Mage::getSingleton('customer/session')->getCustomer();
	    	if($customer->getId()) $customer=Mage::getModel('customer/customer')->load($order->getCustomerId());
	    	else $customer=Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($order->getCustomerEmail());
	    	if($customer->getId()){
	    		
				$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());

	    		//Subtract reward points of customer and save reward points to order if customer use this point to checkout
				$rewardpoints = Mage::getSingleton('checkout/session')->getQuote()->getRewardpoint();
				
	            if($rewardpoints)
	            {
	            	//Subtract reward points of customer
	            	$_customer->addRewardPoint(-$rewardpoints);
	            	$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::USE_TO_CHECKOUT, 'amount'=>(int)$rewardpoints, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
	           		$_customer->saveTransactionHistory($historyData);
	           		$rate = Mage::helper('core')->currency(1,false);
	           		$discount = Mage::getSingleton('checkout/session')->getQuote()->getRewardpointDiscount()/$rate;
	           		//Save reward point for order
	           		$orderData = array('order_id'=>$order->getId(),'reward_point'=>$rewardpoints, 'money'=>$discount,'reward_point_money_rate'=>Mage::helper('rewardpoints')->getPointMoneyRateConfig());
	           		$_order = Mage::getModel('rewardpoints/rewardpointsorder');
	           		$_order->saveRewardOrder($orderData);
	            }
	            
	    		//reward points to customer with order
	    		if($rewardOrder = Mage::getStoreConfig('rewardpoints/config/reward_point_for_order'))
	    		{
		    		$point = explode('/',$rewardOrder);
	            	$_point = $point[0]; 
	            	if(sizeof($point)==2)
	            	{
	            		$total = $order->getGrandTotal();
	            		$rate = Mage::helper('core')->currency(1,false);
	            		$_point = ((int)($total / ($point[1]*$rate))) * $point[0];
	            	}
	            	if($_point >0){
		           		$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::CHECKOUT_ORDER, 'amount'=>$_point,'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::PENDING);
				        $_customer->saveTransactionHistory($historyData);
	            	}
	    		}
	    		
	    		
	    		//reward points to customer with specific product
	    		$product_rewardpoint = Mage::getStoreConfig('rewardpoints/config/enabled_product_reward_point');
	    		if($product_rewardpoint){
		            $quote = Mage::getSingleton('checkout/session')->getQuote();
		            foreach($quote->getAllVisibleItems() as $item)
		            {
		            	$product_id = $item->getProduct()->getId();
						$type = Mage::getModel('catalog/product')->load($product_id) ->getTypeId();
						$parent = 0;
			    		//if($type == 'grouped'){
			    			$parentIds = Mage::getModel('catalog/product_type_grouped')
			    									   ->getParentIdsByChild($product_id);
			                    						//->getChildrenIds($product_id);
			                foreach ($parentIds as $parentId) {
			                	$parent = $parentId;
			                	break;
			                }
			  
			                
			    		//}
			    		if($parent != 0)$product_id = $parent;
		            	//$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
		            	$product = $item->getProduct()->load($product_id);
		            	if($product->getData('reward_point_product'))
		            	{
		           			$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::PURCHASE_PRODUCT, 'amount'=>((int)$product->getData('reward_point_product')) * $item->getQty(), 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$product->getId()."|".$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::PENDING);
		           			$_customer->saveTransactionHistory($historyData);
		            	}
		            }
	    		}
	    		
	    		
	    		if(Mage::helper('rewardpoints')->getInvitationModule())
	    		{
		            //Reward points to friend if this is first purchase
		            $orders = Mage::getModel("sales/order")->getCollection()
		            			->addFieldToFilter('customer_id',$customer->getId());
		            $friend = $_customer->getFriend();
		            if((sizeof($orders) ==1) && ($friend)){
		            	
		            	$point = explode('/',Mage::getStoreConfig('rewardpoints/config/reward_point_for_friend_purchase'));
		            	$_point = $point[0]; 
		            	if(sizeof($point)==2)
		            	{
		            		$total = $order->getGrandTotal();
		            		$_point = ((int)($total / $point[1])) * $point[0];
		            	}
		            	if($_point){
			           		$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE, 'amount'=>(int)$_point, 'balance'=>$friend->getMwRewardPoint(), 'transaction_detail'=>$customer->getId()."|".$order->getId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::PENDING);
			           		$friend->saveTransactionHistory($historyData);
		            	}
		            }else if($friend)
		            {
		            	//Reward points to friend if this is next purchase
		            	$point = explode('/',Mage::getStoreConfig('rewardpoints/config/reward_point_for_friend_next_purchase'));
		            	$_point = $point[0]; 
		            	if(sizeof($point)==2)
		            	{
		            		$total = $order->getGrandTotal();
		            		$_point = ((int)($total / $point[1])) * $point[0];
		            	}
		            	if($_point){
			           		$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE, 'amount'=>(int)$_point, 'balance'=>$friend->getMwRewardPoint(), 'transaction_detail'=>$customer->getId()."|".$order->getId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::PENDING);
			           		$friend->saveTransactionHistory($historyData);
		            	}
		            }
	    		}
	    	}
		}
    }
    public function saveOrderInvoiceAfter($argv)
    {
    	$invoice = $argv->getInvoice();
    	$order = $invoice->getOrder();
    	$customerId = $order->getCustomerId();
    	$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
    	
    	$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customerId)
					->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC')
		;
		
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				//Points for product
				case MW_RewardPoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getIncrementId()) continue;
					
					$customer->addRewardPoint($transaction->getAmount());
					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$transaction->setStatus($status)->save();
					break;
					
				//Add points when first purchase, next purchase
				case MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getIncrementId()) continue;
					$order = Mage::getModel('sales/order')->load($detail[1]);

					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$customer->getFriend()->addRewardPoint($transaction->getAmount());
					$transaction->setBalance($customer->getMwRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
					
				//Use points to check out
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$transaction->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
			
				//Reward points for order
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$customer->addRewardPoint($transaction->getAmount());
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
			}
		}
		
		if($customer->getFriend()){
			//update transaction status for friend
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
						->addFieldToFilter('customer_id',$customer->getFriend()->getId())
						->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
						->addFieldToFilter('type_of_transaction',array('in'=>array(MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE,MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE)))
						->addOrder('transaction_time','ASC')
						->addOrder('history_id','ASC');
			foreach($transactions as $transaction)
			{
				$detail = explode("|",$transaction->getTransactionDetail());
				$order = Mage::getModel('sales/order')->load($detail[1]);
				$status = $transaction->getStatus();
				$customer->getFriend()->addRewardPoint($transaction->getAmount());
				$status = MW_RewardPoints_Model_Status::COMPLETE;
				$transaction->setBalance($customer->getFriend()->getRewardPoint())->setTransactionTime(now());
				$transaction->setStatus($status)->save();
			}
		}
					
    }
    public function paymentCancel($arvgs)
    {
    	$payment = $arvgs->getPayment();
    	$order = $payment->getOrder();
    	
    	$customerId = $order->getCustomerId();
    	$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
    	
    	$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customerId)
					->addFieldToFilter('transaction_detail',array('like'=>"%".$order->getIncrementId()))
					->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::PENDING,MW_RewardPoints_Model_Status::COMPLETE)))
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC')
		;
		
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				//Points for product
				case MW_RewardPoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getId()) continue;

					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$transaction->setStatus($status)->save();
					break;
					
				//Add points when first purchase, next purchase
				case MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getIncrementId()) continue;
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
					
				//Use points to check out
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					// cap nhat lai trang thai uncomplete cho transaction do
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$transaction->setStatus($status)->save();
					
					$customer->addRewardPoint($transaction->getAmount());
					$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_ADD_POINTS, 'amount'=>(int)$transaction->getAmount(), 'balance'=>$customer->getMwRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					$customer->saveTransactionHistory($historyData);
					break;
			
				//Reward points for order
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
			}
		}
    }
    // canceled payment
    // cap nhat trang thai khi payment that bai
 	public function cancelPayment($arvgs)
    {
    	$order = $arvgs->getOrder();
    	
    	$customerId = $order->getCustomerId();
    	$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
    	
    	$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customerId)
					->addFieldToFilter('transaction_detail',array('like'=>"%".$order->getIncrementId()))
					->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::PENDING,MW_RewardPoints_Model_Status::COMPLETE)))
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC')
		;
		
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				//Points for product
				case MW_RewardPoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getId()) continue;

					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$transaction->setStatus($status)->save();
					break;
					
				//Add points when first purchase, next purchase
				case MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getIncrementId()) continue;
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
					
				//Use points to check out
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					// cap nhat lai trang thai uncomplete cho transaction do
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$transaction->setStatus($status)->save();
					
					$customer->addRewardPoint($transaction->getAmount());
					$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_ADD_POINTS, 'amount'=>(int)$transaction->getAmount(), 'balance'=>$customer->getMwRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					$customer->saveTransactionHistory($historyData);
					break;
			
				//Reward points for order
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
			}
		}
    }
    public function orderSaveAfter($arvgs)
    {
    	$order = $arvgs->getOrder();
    	if($order->getStatus() == 'canceled')
		{
			$this ->cancelPayment($arvgs);
		}
		if($order->getStatus() == 'closed')
		{
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('transaction_detail',array('like'=>"%".$order->getIncrementId()))
			->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::PENDING,MW_RewardPoints_Model_Status::COMPLETE)));

			if(sizeof($transactions)) foreach($transactions as $transaction)
			{
				$customer = $transaction->getCustomer();
				switch($transaction->getTypeOfTransaction())
				{
					//Points for product
					case MW_RewardPoints_Model_Type::PURCHASE_PRODUCT:
						//$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
						if($transaction->getStatus() == MW_RewardPoints_Model_Status::COMPLETE){
							$customer->addRewardPoint(-$transaction->getAmount());
							$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_SUBTRACT_PRODUCT_POINTS, 'amount'=>(int)$transaction->getAmount(), 'balance'=>$customer->getMwRewardPoint(), 'transaction_detail'=>$transaction->getTransactionDetail(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
							$customer->saveTransactionHistory($historyData);
						}else{
							$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE)->save();
						}
						break;
						
					//Add points when first purchase, next purchase
					case MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE:
					case MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE:
						$detail = explode("|",$transaction->getTransactionDetail());
						if($detail[1]!= $order->getIncrementId()) continue;
						
						$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE)->save();
						break;
						
					//Use points to check out
					case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
						if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						$customer->addRewardPoint($transaction->getAmount());
						$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_ADD_POINTS, 'amount'=>(int)$transaction->getAmount(), 'balance'=>$customer->getMwRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
						$customer->saveTransactionHistory($historyData);
						break;
				
					//Reward points for order
					case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
						if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;

						if($transaction->getStatus() == MW_RewardPoints_Model_Status::COMPLETE){
							$customer->addRewardPoint(-$transaction->getAmount());
							$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_SUBTRACT_POINTS, 'amount'=>(int)$transaction->getAmount(), 'balance'=>$customer->getMwRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
							$customer->saveTransactionHistory($historyData);
						}else
							$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE)->save();
						break;
				}
			}
		}
    }
    public function checkoutSuccess()
    {
            //Reset Reward Points in Session
            Mage::getSingleton('checkout/session')->unsetData('reward_points');
            Mage::getSingleton('checkout/session')->unsetData('discount');
    }
}