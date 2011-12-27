<?php

class MW_RewardPoints_Model_Rewardpointshistory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/rewardpointshistory');
    }
    public function getCustomer()
    {
    	return Mage::getModel('rewardpoints/customer')->load($this->getCustomerId());
    }
}