<?php

class MW_RewardPoints_Model_Type extends Varien_Object
{
    const REGISTERING				= 1;
    const SUBMIT_PRODUCT_REVIEW		= 2;
	const PURCHASE_PRODUCT			= 3;
	const INVITE_FRIEND				= 4;
	const FRIEND_REGISTERING		= 5;
	const FRIEND_FIRST_PURCHASE		= 6;
	const RECIVE_FROM_FRIEND		= 7;
	const CHECKOUT_ORDER			= 8;
	const SEND_TO_FRIEND			= 9;
	const EXCHANGE_TO_CREDIT		= 10;
	const USE_TO_CHECKOUT			= 11;
	const ADMIN_ADDITION			= 12;
	const EXCHANGE_FROM_CREDIT		= 13;
	const FRIEND_NEXT_PURCHASE		= 14;
	const SUBMIT_POLL				= 15;
	const SIGNING_UP_NEWLETTER		= 16;
	const ADMIN_SUBTRACT			= 17;
	const BUY_POINTS				= 18;
	const SEND_TO_FRIEND_EXPIRED	= 19;
	const REFUND_ORDER				= 20;
	const REFUND_ORDER_ADD_POINTS 	= 21;
	const REFUND_ORDER_SUBTRACT_POINTS 	= 22;
	const REFUND_ORDER_SUBTRACT_PRODUCT_POINTS	= 23;
    static public function getOptionArray()
    {
        return array(
            self::REGISTERING    			=> Mage::helper('rewardpoints')->__('Register'),
            self::SUBMIT_PRODUCT_REVIEW   	=> Mage::helper('rewardpoints')->__('Submit Product Review'),
            self::PURCHASE_PRODUCT    		=> Mage::helper('rewardpoints')->__('Purchase Product'),
            self::INVITE_FRIEND   			=> Mage::helper('rewardpoints')->__('Invite A Friend'),
            self::FRIEND_REGISTERING    	=> Mage::helper('rewardpoints')->__('Friend Registering'),
            self::FRIEND_FIRST_PURCHASE		=> Mage::helper('rewardpoints')->__('Friend First Purchase'),
            self::RECIVE_FROM_FRIEND    	=> Mage::helper('rewardpoints')->__('Receive From Friend'),
            self::SEND_TO_FRIEND   			=> Mage::helper('rewardpoints')->__('Send Points To Friend'),
            self::CHECKOUT_ORDER    		=> Mage::helper('rewardpoints')->__('Checkout An Order'),
            self::EXCHANGE_TO_CREDIT   		=> Mage::helper('rewardpoints')->__('Exchange To Credit'),
            self::USE_TO_CHECKOUT			=> Mage::helper('rewardpoints')->__('Use To Checkout'),
            self::ADMIN_ADDITION			=> Mage::helper('rewardpoints')->__('Add By Admin'),
            self::ADMIN_SUBTRACT			=> Mage::helper('rewardpoints')->__('Subtract By Admin'),
            self::EXCHANGE_FROM_CREDIT		=> Mage::helper('rewardpoints')->__('Exchange From Credit'),
            self::FRIEND_NEXT_PURCHASE		=> Mage::helper('rewardpoints')->__('Friend Purchase'),
            self::SUBMIT_POLL				=> Mage::helper('rewardpoints')->__('Submit Poll'),
            self::SIGNING_UP_NEWLETTER		=> Mage::helper('rewardpoints')->__('Sign Up For Newsletter'),
            self::BUY_POINTS				=> Mage::helper('rewardpoints')->__('Buy Reward Points'),
            self::SEND_TO_FRIEND_EXPIRED	=> Mage::helper('rewardpoints')->__('Send Points To Friend')
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    
    static public function getTransactionDetail($type, $detail = null, $status=null,$is_admin= false)
    {
    	$result = "";
    	switch($type)
    	{
    		case self::REGISTERING:
    			$result = Mage::helper('rewardpoints')->__("Reward for registering");
    			break;
    		case self::SUBMIT_PRODUCT_REVIEW:
				$detail = explode('|',$detail);
    			$review = Mage::getModel('review/review')->load($detail[0]);
				$object = Mage::getModel('catalog/product');
				
				if($review->getId()){
					$object->load($review->getEntityPkValue());
				}else{
					$object->load($detail[1]);
				}
				
				$url = $object->getProductUrl();
    			if($is_admin) $url = Mage::getUrl('adminhtml/catalog_product/edit',array('id'=>$object->getId()));
				$result = Mage::helper('rewardpoints')->__("Reward for reviewing product <b><a href='%s'>%s</a></b>",$url, $object->getName());
				
    			break;
    		case self::PURCHASE_PRODUCT:
    			$_detail = explode('|',$detail);
    			$product_id = $_detail[0];
    			$object = Mage::getModel('catalog/product')->load($product_id);
    			$url = $object->getProductUrl();
    			if($is_admin) $url = Mage::getUrl('adminhtml/catalog_product/edit',array('id'=>$product_id));
    			$result = Mage::helper('rewardpoints')->__("Reward for purchasing product <b><a href='%s'>%s</a></b>",$url, $object->getName());
    			break;
    		case self::INVITE_FRIEND:
    			$result = Mage::helper('rewardpoints')->__("Reward for friend (<b>%s</b>) visit refferal link",$detail);
    			break;	
    		case self::FRIEND_REGISTERING:
    			$object = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('rewardpoints')->__("Reward for friend (<b>%s</b>) registering",$object->getEmail());
    			break;
    		case self::FRIEND_FIRST_PURCHASE:
    			$detail = explode('|',$detail);
    			$object = Mage::getModel('customer/customer')->load($detail[0]);
    			$result = Mage::helper('rewardpoints')->__("Reward for the first purchase of friend (<b>%s</b>)",$object->getEmail());
    			break;
    		case self::FRIEND_NEXT_PURCHASE:
    			$detail = explode('|',$detail);
    			$object = Mage::getModel('customer/customer')->load($detail[0]);
    			$result = Mage::helper('rewardpoints')->__("Reward for purchase of friend (<b>%s</b>)",$object->getEmail());
    			break;
    		case self::RECIVE_FROM_FRIEND:
    			$object = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('rewardpoints')->__("Receive points from friend (<b>%s</b>)",$object->getEmail());
    			break;
    		case self::SEND_TO_FRIEND:
    			$email = $detail;
    			if($status == MW_RewardPoints_Model_Status::COMPLETE){
    				$object = Mage::getModel('customer/customer')->load($detail);
    				$email = $object->getEmail();
    			}
    			
    			$result = Mage::helper('rewardpoints')->__("Send points to friend (<b>%s</b>)",$email);
    			break;
    		case self::CHECKOUT_ORDER:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('rewardpoints')->__("Reward for checkout order <b><a href='%s'>#%s</a></b>",$url,$detail);
    			break;
    		case self::EXCHANGE_TO_CREDIT:
    			$result = Mage::helper('rewardpoints')->__("Exchange to %s credits",round($detail,0));
    			break;
    		case self::USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('rewardpoints')->__("Use to checkout order <b><a href='%s'>#%s</a></b>",$url,$detail);
    			break;
    		case self::ADMIN_ADDITION:
    			$detail = explode('|',$detail);
    			$result = Mage::helper('rewardpoints')->__("Reward from admin: <i>%s</i>",$detail[0]);
    			break;
    		case self::ADMIN_SUBTRACT:
    			$detail = explode('|',$detail);
    			$result = Mage::helper('rewardpoints')->__("Subtract by admin: <i>%s</i>",$detail[0]);
    			break;
    		case self::EXCHANGE_FROM_CREDIT:
    			$result = Mage::helper('rewardpoints')->__("Exchange from credit");
    			break;
    		case self::SUBMIT_POLL:
    			$result = Mage::helper('rewardpoints')->__("Reward for submitting poll");
    			break;
    		case self::SIGNING_UP_NEWLETTER:
    			$result = Mage::helper('rewardpoints')->__("Reward for signing up newsletter");
    			break;
    		case self::SEND_TO_FRIEND_EXPIRED:
    			$result = Mage::helper('rewardpoints')->__("The sendding points to friend(<strong>%s</strong>) was expired",$detail);
    			break;
    		case self::REFUND_ORDER_ADD_POINTS:
				$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('rewardpoints')->__("You get back used points for order <a href='%s'><strong>#%s</strong></a> (refunded)",$url,$detail);
    			break;
    		case self::REFUND_ORDER_SUBTRACT_POINTS:
				$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('rewardpoints')->__("Subtract earned points for order <a href='%s'><strong>#%s</strong></a> (refunded)",$url,$detail);
    			break;
    		case self::REFUND_ORDER_SUBTRACT_PRODUCT_POINTS:
    			$_detail = explode('|',$detail);
    			$product_id = $_detail[0];
    			$object = Mage::getModel('catalog/product')->load($product_id);
    			$url = $object->getProductUrl();
    			if($is_admin) $url = Mage::getUrl('adminhtml/catalog_product/edit',array('id'=>$product_id));
    			$result = Mage::helper('rewardpoints')->__("Subtract earned points for product <b><a href='%s'>%s</a></b> (refunded)",$url, $object->getName());
    			break;
    	}
    	if($is_admin)
    	{
    		$result = str_replace('You','Customer',$result);
    		$result = str_replace('Your','Customer\'s',$result);
    	}
    	return $result;
    }
    
    static public function getAmountWithSign($amount, $type)
    {
    	$result = $amount;
    	switch ($type)
    	{
    		case self::REGISTERING:
    		case self::SUBMIT_PRODUCT_REVIEW:
    		case self::PURCHASE_PRODUCT:
    		case self::INVITE_FRIEND:
    		case self::FRIEND_REGISTERING:
    		case self::FRIEND_FIRST_PURCHASE:
    		case self::FRIEND_NEXT_PURCHASE:
    		case self::RECIVE_FROM_FRIEND:
    		case self::CHECKOUT_ORDER:
    		case self::SUBMIT_POLL:
    		case self::SIGNING_UP_NEWLETTER:
    		case self::ADMIN_ADDITION:
    		case self::BUY_POINTS:
    		case self::REFUND_ORDER_ADD_POINTS:
				$result = "+".$amount;
				break;
    		case self::SEND_TO_FRIEND:
    		case self::EXCHANGE_TO_CREDIT:
    		case self::USE_TO_CHECKOUT:
    		case self::ADMIN_SUBTRACT:
    		case self::REFUND_ORDER_SUBTRACT_POINTS:
    		case self::REFUND_ORDER_SUBTRACT_PRODUCT_POINTS:
    			$result = -$amount;
    		break;
    	}
    	return $result;
    }
}