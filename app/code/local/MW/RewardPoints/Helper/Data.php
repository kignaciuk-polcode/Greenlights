<?php

class MW_RewardPoints_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getInvitationModule()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if(in_array('MW_Invitation',$modules)) return true;
		return false;
	}
	
	public function getCreditModule()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if(in_array('MW_Credit',$modules)) 
		{
			if(Mage::getStoreConfig('credit/config/enabled'))
				return true;
		}
		return false;
	}
	public function getPointCurency()
	{
		return Mage::getStoreConfig('rewardpoints/display/point_curency');
	}
	
	public function formatPoints($points)
	{
		$position = Mage::getStoreConfig('rewardpoints/display/curency_position');
		$_points = number_format($points,0,'.',',');
		if($position == MW_RewardPoints_Model_Position::BEFORE)
		{
			return $this->getPointCurency()." ".$_points;
		}
		
		return $_points." ".$this->getPointCurency();
	}
	
	public function getCheckoutSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	
	public function getCurrentCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
	public function moduleEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/config/enabled');
	}
	
	public function formatMoney($money,$format=true, $includeContainer = true)
	{
		return Mage::helper('core')->currency($money,$format, $includeContainer);
	}
	
	public function exchangePointsToMoneys($rewardpoints)
	{
		$rate = $this->getPointMoneyRateConfig();
		$rate = explode('/',$rate);
	   	$money = ($rewardpoints * 1.0 * $rate[1])/$rate[0];
	   	return $money;
	}
	
	public function exchangeMoneysToPoints($money)
	{
		$rate = $this->getPointMoneyRateConfig();
		$rate = explode('/',$rate);
		$points = ($money * 1.0 * $rate[0]) / $rate[1];
		return $points;
	}
	
	public function getPointMoneyRateConfig()
	{
		return Mage::getStoreConfig('rewardpoints/config/point_money_rate');
	}
	
	public function getMaxPointToCheckOut()
	{
		return Mage::getStoreConfig('rewardpoints/config/max_points_to_checkout');
	}
	
	public function allowSendRewardPointsToFriend()
	{
		return Mage::getStoreConfig('rewardpoints/send_reward_points/allow_send_reward_point_to_friend');
	}
	
	public function enabledCapcha()
	{
		return Mage::getStoreConfig('rewardpoints/capcha/capcha_enabled');
	}
	public function getCapchaBackgroundImage()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/background_image'))
			return Mage::getBaseDir('media').DS.'mw_rewardpoints'.DS.'capcha'.DS. Mage::getStoreConfig('rewardpoints/capcha/background_image');
		return Mage::getDesign()->getSkinBaseDir(array()).DS.'mw_rewardpoints'.DS.'backgrounds'.DS.'bg3.jpg';
	}
	public function getCapchaBackgroundColor()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/image_bg_color'))
			return "#".Mage::getStoreConfig('rewardpoints/capcha/image_bg_color');
		return "#FFFFFF";
	}
	public function getCapchaImageWidth()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/image_width'))
			return Mage::getStoreConfig('rewardpoints/capcha/image_width');
		return 255;
	}
	public function getCapchaImageHeight()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/image_height'))
			return Mage::getStoreConfig('rewardpoints/capcha/image_height');
		return 50;
	}
	public function getCapchaPerturbation()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/perturbation'))
			return Mage::getStoreConfig('rewardpoints/capcha/perturbation');
		return 0.7;
	}
	public function getCapchaCodeLength()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/code_length'))
			return Mage::getStoreConfig('rewardpoints/capcha/code_length');
		return 7;
	}
	public function capchaUseTransparentText()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/use_transparent_text'))
			return Mage::getStoreConfig('rewardpoints/capcha/use_transparent_text');
		return 1;
	}
	public function getCapchaTextTransparencyPercentage()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/text_transparency_percentage'))
			return Mage::getStoreConfig('rewardpoints/capcha/text_transparency_percentage');
		return 0;
	}
	public function getCapchaNumberLine()
	{
		return Mage::getStoreConfig('rewardpoints/capcha/num_lines');
	}
	public function getCapchaTextColor()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/text_color'))
			return "#".Mage::getStoreConfig('rewardpoints/capcha/text_color');
		return '#FF7F27';
	}
	public function getCapchaLineColor()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/line_color'))
			return "#".Mage::getStoreConfig('rewardpoints/capcha/line_color');
		return '#E8E8E8';
	}
	public function capchaUseWordList()
	{
		if(Mage::getStoreConfig('rewardpoints/capcha/use_wordlist'))
			return Mage::getStoreConfig('rewardpoints/capcha/use_wordlist');
		return 0;
	}
	
	public function allowSendEmailToSender()
	{
		return Mage::getStoreConfig('rewardpoints/send_reward_points/enable_send_email_to_sender');
	}
	
	public function allowSendEmailToRecipient()
	{
		return Mage::getStoreConfig('rewardpoints/send_reward_points/enable_send_email_to_recipient');
	}
	public function allowExchangePointToCredit()
	{
		return Mage::getStoreConfig('rewardpoints/exchange_to_credit/enabled');
	}
	
	
	public function getCheckoutRewardPointsRule($quote)
	{
		$rules = array();

		$rewardOrder = Mage::getStoreConfig('rewardpoints/config/reward_point_for_order');
		if($rewardOrder)
    	{
    		$point = explode('/',$rewardOrder);
            $_point = $point[0]; 
            if(sizeof($point)==2)
            {
				 $rate = Mage::helper('core')->currency(1,false);
				// convert price from current currency to base currency
				$total = $quote->getGrandTotal(); 
            	$_point = ((int)($total / ($point[1]*$rate))) * $point[0];
            }
            if($_point >0){
            	if(sizeof($point)==2)
           			$rules[] = array('message'=>Mage::helper('rewardpoints')->__('%s for <b>%s grand total</b> of this order',$this->formatPoints($_point),$this->formatMoney($quote->getGrandTotal()/$rate)),'amount'=>$_point, 'qty'=>1);
            	else $rules[] = array('message'=>Mage::helper('rewardpoints')->__('%s for this order',$this->formatPoints($_point)),'amount'=>$_point, 'qty'=>1);
            }
    	}
    	$product_rewardpoint = Mage::getStoreConfig('rewardpoints/config/enabled_product_reward_point');
    	if($product_rewardpoint){
			foreach($quote->getAllItems() as $item)
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
				//$product = $item->getProduct()->load($item->getProduct()->getId());
				$product = $item->getProduct()->load($product_id);
				if($product->getData('reward_point_product'))
				{
					$rules[] = array('message'=>$this->__('%s for product: <b>%s</b>',$this->formatPoints($product->getData('reward_point_product')),$product->getName()),'amount'=>$product->getData('reward_point_product'),'qty'=>$item->getQty());
				}
			}
    	}
		return $rules;
	}
	
	public function roundPoints($points,$up = true)
    {
		$config = Mage::getStoreConfig('rewardpoints/config/point_money_rate');
		$rate = explode("/",$config);
		$tmp = (int)($points/$rate[0]) * $rate[0];
		if($up)
			return $tmp<$points?$tmp+$rate[0]:$tmp;
		return $tmp;
    }
    
    public function formatNumber($value)
    {
    	return number_format($value,0,'.',',');
    }
}