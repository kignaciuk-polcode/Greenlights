<?php

class Ebizmarts_Mailchimp_Model_Mailchimp extends Ebizmarts_Mailchimp_Model_MCAPI {

	const ACTION_SUBSCRIBE     	 = 'subscribe';
	const ACTION_SILENTSUBSCRIBE = 'silentSubscribe';
	const ACTION_RESUBSCRIBE     = 'resubscribe';
	const ACTION_UPDATE     	 = 'update';
	const ACTION_SILENTUPDATE	 = 'silentUpdate';
	const ACTION_UNSUBSCRIBE     = 'unsubscribe';

	private function setSubsParams(){

		$helper = Mage::helper('mailchimp');

		$this->setEmailType((string)$helper->getSubscribeConfig('email_type',$this->getStore()));
		// if user subscribes own login email - confirmation is not needed
		$this->setDoubleOptin(($this->getCustomer()->getEntityId())? (bool)0 : (bool)$helper->getSubscribeConfig('double_optin',$this->getStore()));
		$this->setUpdateExisting((bool)$helper->getSubscribeConfig('update_existing',$this->getStore()));
		if($this->getCustomer()->getAction() == self::ACTION_UNSUBSCRIBE){
			$this->setReplaceInterests((bool)1);
		}else{
			$this->setReplaceInterests((bool)$helper->getSubscribeConfig('replace_interests',$this->getStore()));
		}
		$this->setSendWelcome(($this->getCustomer()->getisSilentUpdate())? (bool)0 : (bool)$helper->getSubscribeConfig('send_welcome',$this->getStore()));

		return $this;
	}

	private function setUnSubsParams(){

		$helper = Mage::helper('mailchimp');

		$this->setDeleteMember((bool)$helper->getUnSubscribeConfig('delete_member',$this->getStore()));
		$this->setSendGoodbye(($this->getCustomer()->getisSilentUpdate())? (bool)0 :(bool)$helper->getUnSubscribeConfig('send_goodbye',$this->getStore()));
		$this->setSendNotify(($this->getCustomer()->getisSilentUpdate())? (bool)0 : (bool)$helper->getUnSubscribeConfig('send_notify',$this->getStore()));

		return $this;
	}

	private function subscribeCustomer(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);
		$this->setSubsParams();

		$merge_vars = Mage::helper('mailchimp')->getMergeVars($this->getCustomer(),false);
		$retval = $this->listSubscribe($this->getListId(),
									   $this->getEmail(),
									   $merge_vars,
									   $this->getEmailType(),
									   $this->getDoubleOptin(),
									   $this->getUpdateExisting(),
									   $this->getReplaceInterests(),
									   $this->getSendWelcome());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		$this->registerSubscription();
		return $retval;
	}

	private function unSubscribeCustomer(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);
		$this->setUnSubsParams();

		$retval = $this->listUnsubscribe($this->getListId(),
										 $this->getCustomer()->getIdentifier(),
										 $this->getDeleteMember(),
										 $this->getSendGoodbye(),
										 $this->getSendNotify());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		$this->registerSubscription();
		return $retval;
	}

	private function updateCustomer(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);

		$this->setSubsParams();
		$merge_vars = Mage::helper('mailchimp')->getMergeVars($this->getCustomer(),true);

		$retval = $this->listUpdateMember($this->getListId(),
									      $this->getCustomer()->getIdentifier(),
									      $merge_vars,
									      $this->getEmailType(),
									      $this->getReplaceInterests());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		$this->registerSubscription();
		return $retval;
	}

	public function getListMemberInfo(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);

		$retval = $this->listMemberInfo($this->getListId(),$this->getEmail());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		if($retval['success'] > 0){
			return $retval['data'][0];
		}

		return false;
	}

	private function getListInterestGroupings(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);

		$retval = $this->listInterestGroupings($this->getListId());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		return $retval;
	}

	public function getLists(){

		$scope = ($this->getScope())? $this->getScope() : Mage::app()->getStore()->getStoreId();
		$apikey = Mage::helper('mailchimp')->getApiKey($scope);
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);

		$retval = $this->lists();
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		return $retval;
	}

	public function mainAction(){

		$action = $this->getCustomer()->getAction();
		if($action == self::ACTION_SUBSCRIBE || $action == self::ACTION_RESUBSCRIBE || $action == self::ACTION_SILENTSUBSCRIBE){
			$this->subscribeCustomer();
		}elseif($action == self::ACTION_UPDATE){
			$this->updateCustomer();
		}elseif($action == self::ACTION_SILENTUPDATE){
			$response = $this->subscribeCustomer();
			if($response){
				$response = $this->updateCustomer();
				if($response){
					$this->unSubscribeCustomer();
				}
			}
		}elseif($action == self::ACTION_UNSUBSCRIBE){
			$response = $this->updateCustomer();
			if($response){
				$this->unSubscribeCustomer();
			}
		}
	}

	public function getGroupsByListId(){

		$listgroups = $this->getListInterestGroupings();

		if($listgroups){
			$registered = '';

			if($this->getCustomerSession()->getId()){
				$subscriber = Mage::getSingleton('customer/customer')->load($this->getCustomerSession()->getId());
				$subscriber->setCustomerId($subscriber->getEntityId())
						   ->setSubscriberEmail($subscriber->getEmail())
						   ->setListId($this->getListId());
				$registered = Mage::helper('mailchimp')->isMailchimpSubscribed($subscriber)->getData();
			}


			if(!empty($registered)){
				$this->setEmail($subscriber->getEmail());
				$member = $this->getListMemberInfo();

				if(isset($member['id'])){
					$group = array();
					foreach($member['merges']['GROUPINGS'] as $val){
						if($val) $group = array_merge($group, explode(', ',$val['groups']));
			        }
			        foreach($group as $k=>$v){
			        	if(substr($v,-1) == '\\'){
		        			$a = $k+1;
			        		$group[$k] = str_replace('\\','',$v).', '.$group[$a];
			        		unset($group[$a]);
			        	}
			        }
					if(count($group)){
						foreach($listgroups as $ky=>$item){
							foreach($item['groups'] as $k=>$val){
								if(in_array($val['name'],$group)){
									$listgroups[$ky]['groups'][$k]['checked'] = true;
								}
							}
						}
					}
				}
			}
			return $listgroups;
		}
        return '';
	}

	private function registerSubscription(){

		$member = $this->getListMemberInfo();
		$memberId = (isset($member['id']))? $member['id']: $this->getCustomer()->getEmail();
		$this->getCustomer()->setMemberId($memberId);
		$this->getCustomer()->setIdentifier($memberId);
		if($this->getCustomer()->getRegistered()) $this->getCustomer()->setMailchimpproId($this->getCustomer()->getRegistered()->getMailchimpproId());
		Mage::getSingleton('mailchimp/subscripter')->registerInfo($this->getCustomer());

		return true;
	}

	public function getCtemplates(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}
		$this->MCAPI($apikey);
		$this->setTypes(array('user'=>(bool)true,
							  'gallery'=>(bool)true,
							  'base'=>(bool)true));
		$this->setCategory(null);
		$this->setInactives(array('include'=>(bool)true,
								  'only'=>(bool)false));

		$retval = $this->templates($this->getTypes(),
								   $this->getCategory(),
								   $this->getInactives());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		return $retval;

	}

	public function getTemplateInfo(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey || !$this->getTemplateId()){
			return false;
		}
		$this->MCAPI($apikey);

		$retval = $this->templateInfo($this->getTemplateId(),
								      $this->getTid());
		if ($this->errorCode){
			$this->setErrorOutput();
			return false;
		}
		$template = new Varien_Object;
	  	$template->addData($retval);
	  	$template->setId($this->getTemplateId());

		return $template;

	}
	private function setErrorOutput(){

		$this->setCode($this->errorCode);
		$this->setMessage($this->errorMessage);

		Mage::helper('mailchimp')->addException($this);

		unset($this->errorCode, $this->errorMessage);

		return $this;
	}
}
