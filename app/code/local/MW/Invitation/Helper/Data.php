<?php

class MW_Invitation_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function enabled()
	{
		return Mage::getStoreConfig('invitation/config/enabled');
	}
	
	//get Invitation link of customer.
	public function getLink(Mage_Customer_Model_Customer $customer)
	{
		$referral_by = Mage::getStoreConfig('invitation/config/referreal_by');
		switch ($referral_by){
			case MW_Invitation_Model_Invitation::BY_ID:
				$referral_link = trim(Mage::getUrl('invitation'),"/")."?c=".$customer->getId();
				break;
			case MW_Invitation_Model_Invitation::BY_EMAIL:
				$referral_link = trim(Mage::getUrl('invitation'),"/")."?c=".$customer->getEmail();
				break; 
		}
		
		return $referral_link;
	}
	
	public function rewardPointsEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/config/enabled');
	}
}