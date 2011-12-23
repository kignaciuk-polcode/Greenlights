<?php
class MW_RewardPoints_Block_Rewardpoints extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    public function getTitle()
    {
    	return $this->__("Reward Points Management");
    }
}