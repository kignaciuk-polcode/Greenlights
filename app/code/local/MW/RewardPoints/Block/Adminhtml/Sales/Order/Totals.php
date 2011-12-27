<?php
class MW_RewardPoints_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{

    protected function _initTotals()
    {
		parent::_initTotals();
    	$rewardpoints = Mage::getModel('rewardpoints/rewardpointsorder')->load($this->getOrder()->getId());
		if($rewardpoints->getMoney()){
			$total = new Varien_Object(array(
	                'code'      => 'rewardpoints',
	                'value'     => Mage::helper('rewardpoints')->formatPoints($rewardpoints->getRewardPoint()),
	                'base_value'=> Mage::helper('rewardpoints')->formatPoints($rewardpoints->getRewardPoint()),
	                'label'     => Mage::helper('rewardpoints')->__('Spent Points'),
					'strong'    => true,
					'is_formated'=> true,
	            ));
			$this->addTotal($total,'first');
			$total1 = new Varien_Object(array(
	                'code'      => 'rewardpoints_discount',
	                'value'     => $rewardpoints->getMoney(),
	                'base_value'=> $rewardpoints->getMoney(),
	                'label'     => Mage::helper('rewardpoints')->__('Discount'),
	            ));
			$this->addTotal($total1,'subtotal');
		}
        return $this;
    }
}
