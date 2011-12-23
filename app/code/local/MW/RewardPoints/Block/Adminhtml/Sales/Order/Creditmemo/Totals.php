<?php
class MW_RewardPoints_Block_Adminhtml_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals
{

    protected function _initTotals()
    {
		parent::_initTotals();
    	$rewardpoints = Mage::getModel('rewardpoints/rewardpointsorder')->load($this->getOrder()->getId());
    	if($rewardpoints->getMoney()){
			$total = new Varien_Object(array(
	                'code'      => 'rewardpoints_discount',
	                'value'     => Mage::helper('rewardpoints')->formatPoints($rewardpoints->getRewardPoint()),
	                'base_value'=> Mage::helper('rewardpoints')->formatPoints($rewardpoints->getRewardPoint()),
	                'label'     => 'Spent Points',
					'strong'    => true,
					'is_formated'=> true,
	            ));
			$this->addTotal($total,'first');
		}
        return $this;
    }
}
