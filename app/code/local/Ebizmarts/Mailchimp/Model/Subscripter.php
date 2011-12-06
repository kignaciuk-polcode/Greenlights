<?php

class Ebizmarts_Mailchimp_Model_Subscripter extends Mage_Core_Model_Abstract{

	public function _construct(){

		parent::_construct();
  		$this->_init('mailchimp/subscripter');
	}

	public function getListsByCustomer($customer){

		$col = $this->getCollection()
			   ->addFieldToFilter('store_id',$customer->getStoreId())
			   ->addFieldToFilter('customer_id',$customer->getCustomerId())
			   ->addFieldToSelect('list_id');

		/**************could be updating a new customer already subscripted*********************/
		if(!count($col)){
			$col = $this->getCollection()
				   ->addFieldToFilter('store_id',$customer->getStoreId())
				   ->addFieldToFilter('current_email',$customer->getEmail())
				   ->addFieldToSelect('list_id');
		}
		/**************could be updating a new customer already subscripted*********************/

		$lists = array();
		foreach($col as $list){
			$lists[$list->getListId()] = $list->getIsSubscribed();
		}
		return $lists;
	}

	public function prepareCustomer($data){

		$email = (isset($data['data']['email']))? $data['data']['email'] : $data['data']['old_email'] ;

		$customer = Mage::getSingleton('customer/customer')
							->setWebsiteId(Mage::app()->getDefaultStoreView()->getWebsiteId())
							->loadByEmail($email);

		if(!$customer->getEntityId()){
			$customer = Mage::getModel('customer/customer');
			$customer->setEntityId('0')
					 ->setEmail($email)
					 ->setStoreId(Mage::app()->getDefaultStoreView()->getStoreId());
		}

		$memberId = (isset($data['data']['id']))? $data['data']['id'] : $data['data']['new_id'] ;

		$customer->setAction($data['type'])
				 ->setListId($data['data']['list_id'])
				 ->setMemberId($memberId);

		$subscripter = $this->getCollection()
							->addFieldToFilter('list_id',$data['data']['list_id'])
							->addFieldToFilter('current_email',$email)
							->getLastItem();

		$customer->setMailchimpproId($subscripter->getMailchimpproId());

		if(!$subscripter->getMailchimpproId() && ($data['type'] == 'upemail' || $data['type'] == 'profile')){
			$customer->setAction('subscribe');
		}
		if(isset($data['data']['new_email'])){
			$newEmail = $data['data']['new_email'];
		}elseif(isset($data['data']['merges']['EMAIL'])){
			$newEmail = $data['data']['merges']['EMAIL'];
		}else{
			$newEmail = $customer->getEmail();
		}

		$customer->setEmail($newEmail);
		return $customer;
	}

	public function changeStatus($customer){

		$sub = $this->getCollection()
					->addFieldToFilter('store_id',$customer->getStoreId())
					->addFieldToFilter('current_email',$customer->getEmail())
					->addFieldToFilter('list_id',$customer->getListId())
					->getLastItem();

		if($sub->getData()){
			$customer->setMailchimpproId($sub->getMailchimpproId());
			$customer->setMemberId($sub->getMemberId());
			if(($customer->getAction() == Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_UNSUBSCRIBE && (bool)$sub->getIsSubscribed() == false)
				|| ($customer->getAction() == Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_SUBSCRIBE && (bool)$sub->getIsSubscribed() == true)){
				return (int)0;
			}
		}else{
			if($customer->getAction() == Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_UNSUBSCRIBE){
				return (int)0;
			}
		}

		$this->registerInfo($customer);

		return (int)1;
	}

	public function registerInfo($customer){

		if($customer->getMailchimpproId()){
			$this->load($customer->getMailchimpproId());
			$this->setCustomerId($customer->getCustomerId())
				 ->setCurrentEmail($customer->getEmail());
			if($customer->getAction() == Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_UNSUBSCRIBE ||
			   $customer->getAction() == Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_SILENTUPDATE){
				$this->setIsSubscribed((bool)false);
			}else{
			 	$this->setIsSubscribed((bool)true);
			}
		}else{
			$this->setCustomerId($customer->getCustomerId())
		         ->setIsSubscribed(($customer->getAction() == Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_SILENTSUBSCRIBE)? (bool)false : (bool)true)
		         ->setCurrentEmail($customer->getEmail())
	      		 ->setListId($customer->getListId())
		      	 ->setStoreId($customer->getStoreId())
		      	 ->setCreatedTime(date("Y-m-d H:i:s",time()));
		}

		$this->setMemberId($customer->getMemberId())
		     ->setUpdatedTime(date("Y-m-d H:i:s",time()))
		     ->save();
	}
}
