<?php

class MW_RewardPoints_Model_Mysql4_Rewardpointsorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the rewardpoints_id refers to the key field in your database table.
        $this->_init('rewardpoints/rewardpointsorder', 'order_id');
    }
}