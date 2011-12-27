<?php
class MW_RewardPoints_Model_Poll_Vote extends Mage_Poll_Model_Poll_Vote
{
    protected $_eventPrefix = 'poll_vote';
    protected $_eventObject = 'vote';
    
	protected function _construct()
    {
        return parent::_construct();
    }
    
    public function voteAfterSave($argv)
    {
    	$vote = $argv->getVote();
    	if($vote->getCustomerId())
    	{
    		$points = Mage::getStoreConfig('rewardpoints/config/reward_point_for_submit_poll');
    		if($points)
    		{
    			$_customer = Mage::getModel("rewardpoints/customer")->load($vote->getCustomerId());
    			$_customer->addRewardPoint($points);
    			$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SUBMIT_POLL, 'amount'=>(int)$points, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$vote->getPollId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
	            $_customer->saveTransactionHistory($historyData);
	            Mage::getSingleton('core/session')->addSuccess(Mage::helper("rewardpoints")->__("You have been rewarded %s %s for submitting poll",$points,Mage::helper('rewardpoints')->getPointCurency()));
    		}
    	}
    }
}
