<?php

class MW_RewardPoints_Model_Customer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/customer');
    }
	
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * 
     * @param $data=array('type_of_transaction'=>y, 'amount'=>z, 'transaction_detail'=>a, 'transaction_time'=>b)
     */
	
    public function saveTransactionHistory($data = array())
    {
    	$data['customer_id'] = $this->getId();
    	$history = Mage::getModel('rewardpoints/rewardpointshistory');
    	$history->setData($data);
    	$history->save();
    }
    
	public function getRewardPoint()
	{
		return $this->getMwRewardPoint();
	}
	
	public function addRewardPoint($point)
	{
		$this->setMwRewardPoint($this->getMwRewardPoint()+ $point);
		$this->save();
	}
	
	public function getFriend()
	{
		if($this->getMwFriendId())
			return Mage::getModel('rewardpoints/customer')->load($this->getMwFriendId());
		return false;
	}
	
	public function getCustomerModel()
	{
		return Mage::getModel('customer/customer')->load($this->getId());
	}
	
	public function customerSaveAfter($param)
	{
		$customer = $param->getCustomer();
		$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
		
		if(Mage::helper('rewardpoints')->moduleEnabled() && !($_customer->getId()))
		{
            //Check invition information if exist add reward point to friend
        	$friend_id = Mage::getModel('core/cookie')->get('friend');
            if($friend_id && Mage::helper('rewardpoints')->getInvitationModule())
            {
	            $friend = Mage::getModel('rewardpoints/customer')->load($friend_id);
	            $point = Mage::getStoreConfig('rewardpoints/config/reward_point_for_friend_registering');
	            if($friend->getId() && $point)
	            {
		            $friend->setMwRewardPoint($friend->getMwRewardPoint() + $point);
		            $friend->save();
		            $historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_REGISTERING, 'amount'=>$point, 'balance'=>$friend->getMwRewardPoint(), 'transaction_detail'=>$customer->getId(), 'transaction_time'=>now(),'status'=>MW_RewardPoints_Model_Status::COMPLETE);
		            $friend->saveTransactionHistory($historyData);
	            }
            }
			
			//init reward points of customer
			$_customer = Mage::getModel('rewardpoints/customer')->getCollection();
            $point = Mage::getStoreConfig('rewardpoints/config/reward_point_for_registering');
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = 'INSERT INTO '.$_customer->getTable('customer').'(customer_id,mw_reward_point,mw_friend_id) VALUES('.$customer->getId().',0,'. (($friend_id && Mage::helper('rewardpoints')->getInvitationModule())?$friend_id:0).')';
            $write->query($sql);
			//Save history transaction
			if($point){
				$_customerModel = Mage::getModel('rewardpoints/customer')->load($customer->getId());
				$_customerModel->setMwRewardPoint($point);
				$_customerModel->save();
				$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REGISTERING, 'amount'=>$point, 'balance'=>$_customerModel->getMwRewardPoint(), 'transaction_detail'=>'', 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
				$_customerModel->saveTransactionHistory($historyData);
				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('rewardpoints')->__('You recived %s %s points for signing up.',$point, Mage::helper('rewardpoints')->getPointCurency()));
			}
			Mage::dispatchEvent('customer_account_registed_rewardpoint');
		}
	}
}