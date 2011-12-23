<?php
class MW_RewardPoints_InvitationController extends Mage_Core_Controller_Front_Action
{
    public function checkAction()
    {
		if(Mage::helper('rewardpoints')->getInvitationModule())
		{
			$this->getResponse()->setBody("1");
		}else{
			$this->getResponse()->setBody("0");
		}
    }
}