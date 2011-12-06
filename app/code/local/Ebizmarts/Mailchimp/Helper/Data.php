<?php
class Ebizmarts_Mailchimp_Helper_Data extends Mage_Core_Helper_Abstract{

	const BULK_URL    = '/mailchimp/adminhtml_bulkSync/';

	public function getGeneralConfig($field,$store=null){

		return Mage::getStoreConfig('mailchimp/general/'.$field,$store);
	}

	public function getSubscribeConfig($field,$store=null){

		return Mage::getStoreConfig('mailchimp/subscribe/'.$field,$store);
	}

	public function getUnSubscribeConfig($field,$store=null){

		return Mage::getStoreConfig('mailchimp/unsubscribe/'.$field,$store);
	}

	public function getUserAgent(){

		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;

		$aux = (array_key_exists('Enterprise_Enterprise',$modulesArray))? 'EE' : 'CE' ;
		$version = strpos(Mage::getVersion(),'-')? substr(Mage::getVersion(),0,strpos(Mage::getVersion(),'-')) : Mage::getVersion();
		return (string)'Ebizmarts/Mage'.$aux.$version.'/'.$this->getGeneralConfig('version');
	}

	public function getApiKey(){

		$apikey = $this->getGeneralConfig('apikey',Mage::app()->getStore()->getStoreId());
		if(!$apikey){
			Mage::getSingleton('adminhtml/session')->addNotice($this->__('The Ebizmarts MailChimp API Key field is empty.'));
			return false;
		}

	    $dc = "us1";
	    if (strstr($apikey,"-")){
        	list($key, $dc) = explode("-",$apikey,2);
            if (!$dc) $dc = "us1";
        }
        $host = "http://".$dc."."."api.mailchimp.com/";

        try{
	        $url=fopen($host,"r");
	        if($url){
         	    fclose ($url);
    	    }
        }catch (Exception  $e) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Ebizmarts MailChimp General Error, the API Key is not well formed.'));
			return false;
        }
		//if(substr($apikey, -4) != '-us1' && substr($apikey, -4) != '-us2'){}
		return $apikey;
	}

	public function mailChimpEnabled($store){

		if((bool)$this->getGeneralConfig('active',$store) == true &&
			$this->getGeneralConfig('apikey',$store) &&
			$this->getGeneralConfig('general',$store)){
			return true;
		}
		return false;
	}

	public function isEcomm360Activated(){

		$store = Mage::app()->getStore()->getStoreId();
		if($this->mailChimpEnabled($store) && (bool)$this->getGeneralConfig('ecomm360',$store) == true){
			return true;
		}
		return false;
	}

	public function isStsActivated(){

		$store = Mage::app()->getStore()->getStoreId();
		if($this->mailChimpEnabled($store) && (bool)$this->getGeneralConfig('sts',$store) == true){
			return true;
		}
		return false;
	}

	public function checkSendSubscribe(){

		$store = Mage::app()->getStore()->getStoreId();
		$current = Mage::helper('core/url')->getCurrentUrl();
		$return = true;

		if($this->mailChimpEnabled($store)){
			$return = ($this->getSubscribeConfig('double_optin',$store)
					|| $this->getSubscribeConfig('send_welcome',$store)
					|| $this->getSubscribeConfig('success_disabled',$store)
					|| (bool)strpos($current, self::BULK_URL))? false : true;
		}

		return $return;
	}

	public function checkSendUnsubscribe(){

		$store = Mage::app()->getStore()->getStoreId();
		$current = Mage::helper('core/url')->getCurrentUrl();
		$return = true;

		if($this->mailChimpEnabled($store)){
			$return = ((bool)$this->getUnSubscribeConfig('send_notify',$store)
					|| (bool)$this->getUnSubscribeConfig('send_goodbye',$store)
					|| (bool)strpos($current, self::BULK_URL))? false : true;
		}

		return $return;
	}

	public function isSubscribed($email){

		return Mage::getModel('newsletter/subscriber')
					->loadByEmail($email)
					->isSubscribed();
	}

	public function isMailchimpSubscribed($subscriber){

		$currentEmail = ($subscriber->getOldEmail())? $subscriber->getOldEmail() : $subscriber->getSubscriberEmail();

		$col = Mage::getModel('mailchimp/subscripter')->getCollection()
				->addFieldToFilter('store_id',$subscriber->getStoreId())
				->addFieldToFilter('list_id',$subscriber->getListId())
				->addFieldToFilter('current_email',$currentEmail)
				->getLastItem();

		return $col;
	}

	public function getAvailableLists($currentStore){

		if($this->mailChimpEnabled($currentStore)){
			$avlists = explode(',',$this->getGeneralConfig('listid',$currentStore));
			$lists = array();
			foreach($avlists as $list){
				if($list != $this->getGeneralConfig('general',$currentStore)){
					$lists[$list] = $list;
				}
			}

			if(count($lists)){
				$aux = Mage::getSingleton('mailchimp/source_lists')->toOptionArray();
				foreach($lists as $list){
					if(array_key_exists($list,$aux)){
						$lists[$list] = $aux[$list]['label'];
					}
				}
			}
			return $lists;
		}
		return false;
	}

	public function getMergeVars($customer,$flag){

		$merge_vars = array();
		$maps = explode('<',$this->getSubscribeConfig('mapping_fields',$customer->getStoreId()));
		foreach($maps as $map){
			if($map){
				$aux = substr(strstr($map,"customer='"),10);
				$customAtt = (string)substr($aux,0,strpos($aux,"'"));
				$aux = substr(strstr($map,"mailchimp='"),11);
				$chimpTag = (string)substr($aux,0,strpos($aux,"'"));
				if($chimpTag && $customAtt){
					if($customAtt == 'address'){
						$address = $customer->getAddress();
						$merge_vars[strtoupper($chimpTag)] = array('addr1'=>$address['street'],
																   'addr2'=>'',
																   'city'=>$address['city'],
																   'state'=>$address['region'],
																   'zip'=>$address['postcode'],
																   'country'=>$address['country_id']);
					/*****this code has been added thanks to phroggar*****************************/
					}elseif($customAtt == 'date_of_purchase'){
						$orders = Mage::getResourceModel('sales/order_collection')
	                        ->addFieldToFilter('customer_id', $customer->getEntityId())
	                        ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
	                        ->setOrder('created_at', 'desc');
	                    if (($last_order = $orders->getFirstItem()) && (!$last_order->isEmpty())){
	                      $merge_vars[strtoupper($chimpTag)] = Mage::helper('core')->formatDate($last_order->getCreatedAt());
	                    }
                	/*****this code has been added thanks to phroggar*****************************/
					}else{
						if($value = (string)$customer->getData(strtolower($customAtt))) $merge_vars[strtoupper($chimpTag)] = $value;
					}
				}
			}
		}
		if($flag) $merge_vars['EMAIL'] = $customer->getEmail();

		$groups = $customer->getListGroups();
		$groupings = array();
		if(is_array($groups) && count($groups)){
			foreach($groups as $option){
				$parts = explode(']',str_replace('[','',$option));
				if($parts[0] == $customer->getListId() && count($parts) == 5){
					$groupings[] = array('id'=>$parts[2],
									   'name'=>str_replace(',','\,',$parts[1]),
									   'groups'=>str_replace(',','\,',$parts[3]));
				}
			}
		}
		$merge_vars['GROUPINGS'] = $groupings;
		return $merge_vars;
	}

	private function getAllListGroups($params,$currentStore){

		$allGrps = array();
		if($this->getGeneralConfig('intgr',$currentStore)){
			$params = (is_array($params))? $params : $allGrps;

			if(isset($params['group']) || isset($params['allgroups'])){

				$groups = (isset($params['group']) && count($params['group']))? $params['group'] : array();
				$arrayCheck = array();

				foreach($groups as $list=>$group){
					if(is_array($group)){
						foreach($group as $checkbox){
							if(is_array($checkbox)){
								foreach($checkbox as $item){
									$allGrps[] = '['.$list.']'.str_replace('|','][',$item);
									$aux = substr($item,0,strpos($item,']')).']';
									if(!in_array($aux,$arrayCheck)) $arrayCheck[] = $aux;
								}
							}else{
								if($checkbox){
									if(strstr($checkbox,']')){
										$allGrps[] = '['.$list.']'.$checkbox;
									}else{
										$allGrps[] = '['.$list.']'.str_replace('|','][',$checkbox).'][]';
									}
								}
							}
						}
					}else{
						if($group && strstr($group,'][')){
							$allGrps[] = '['.$list.']'.str_replace('|','][',$group);
						}else{
							$allGrps[] = '['.$list.']'.str_replace('|','][',$params['allgroups'][$list]).'[]';
						}
					}
				}
				if(isset($params['allgroups'])){
					foreach($params['allgroups'] as $list=>$value){
						$parts = explode(']',str_replace('[','',$value));
						foreach($parts as $item){
							if($item && !in_array('['.$item.']',$arrayCheck)){
								$allGrps[] = '['.$list.']['.str_replace('|','][',$item).'][]';
							}
						}
					}
				}
			}
		}
		return $allGrps;
	}

	public function preFilter($email,$params){
		$subscriber = Mage::getSingleton('newsletter/subscriber')->loadByEmail($email);
		if(!$subscriber->getData()){
			$customSession = Mage::getSingleton('customer/session')->getCustomer();
			$subscriber = Mage::getSingleton('customer/customer')
							->setWebsiteId($customSession->getWebsiteId())
							->loadByEmail($email);
			if(!$subscriber->getData()){
				$subscriber = $customSession;
				if(!$subscriber->getEmail()){
					$subscriber->setStoreId(Mage::app()->getStore()->getStoreId())
							   ->setCustomerId(0)
							   ->setEmail($email);
				}
			}else{
				$subscriber->setCustomerId($subscriber->getEntityId());
			}
			if(isset($params['oldEmail'])) $subscriber->setOldEmail($params['oldEmail']);
			return $this->mailChimpFilter($subscriber,$params);

		}else{
			if(isset($params['oldEmail'])) $subscriber->setOldEmail($params['oldEmail']);
			return true;
		}

	}

	public function preAdminFilter($params){

		$customer = Mage::getSingleton('customer/customer')->load($params['id']);

		if(Mage::registry('oldEmail')){
			$params['oldEmail'] = Mage::registry('oldEmail');
		}

		$email = $params['account']['email'];
		$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);

		if(!$subscriber->getData()){
			return true;
		}

		if(Mage::registry('oldEmail')) $subscriber->setOldEmail($params['oldEmail']);

		if(strstr($params['tab'],'newsletter') == 'newsletter'){
			$params['is_general'] = true;
		}else{
			$params['onlyUpdate'] = true;
		}
		$this->mailChimpFilter($subscriber,$params);

		return true;
	}

	protected function mailChimpFilter($subscriber,$params){

		if($this->mailChimpEnabled($subscriber->getStoreId())){

			$allGrps = $this->getAllListGroups($params,$subscriber->getStoreId());
			$subscriber->setListGroups($allGrps);

			if($subscriber->getCustomerId()){
				$customer = Mage::getSingleton('customer/customer')->load($subscriber->getCustomerId())->setSoreId($subscriber->getStoreId());

				foreach($customer->getData() as $k=>$v){
					if($customer->getAttribute($k) && $customer->getAttribute($k)->usesSource()){
						$options = $customer->getAttribute($k)->getSource()->getAllOptions();
				        $value = $customer->getData($k);
				        foreach ($options as $option){
				            if($option['value'] == $value) $v = $option['label'];
				        }
					}
					$subscriber->setData($k,$v);
				}

				$address = ($customer->getDefaultBillingAddress())? $customer->getDefaultBillingAddress() : Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress() ;

				if($address->getStreet()){
					$addressArray = array();
					foreach($address->getData() as $k=>$v){
						$addressArray[$k] = $v;
					}
					$subscriber->setAddress($addressArray);
				}

			}else{
				$billing = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();

				if((string)$billing->getEmail() == (string)$subscriber->getEmail()){
					$address = array();
					foreach($billing->getData() as $k=>$v){
						$subscriber->setData($k,$v);
						$address[$k] = $v;
					}
					$subscriber->setAddress($address);
				}else{
					$subscriber->setFirstname($this->getSubscribeConfig('guest_name',$subscriber->getStoreId()))
							   ->setLastname($this->getSubscribeConfig('guest_lastname',$subscriber->getStoreId()));
				}
			}

			if(isset($params['is_general'])){
				$subscriber->setListId($this->getGeneralConfig('general',$subscriber->getStoreId()));
				$this->doTheAction($subscriber);
			}elseif(isset($params['onlyUpdate'])){
				$list = Mage::getSingleton('mailchimp/subscripter')->getListsByCustomer($subscriber);
				$subscriber->setIsOnlyUpdate(true);
				foreach($list as $k=>$v){
					if($v == (bool)0){
						$subscriber->setisSilentUpdate(true);
					}
					$subscriber->setListId($k);
					$this->doTheAction($subscriber);
				}
			}elseif(isset($params['additional'])){
				$subscriber->setIsAdditionalList(true);
				$allLists = $this->getAvailableLists($subscriber->getStoreId());
				$lists = (isset($params['list']))? $params['list']: array($this->getGeneralConfig('general',$subscriber->getStoreId())=>(int)1);
				foreach($allLists as $k=>$v){
					if($k && !array_key_exists($k,$lists)){
						$subscriber->setToUnsubscribe(true);
					}
					$subscriber->setListId($k);
					$this->doTheAction($subscriber);
				}
			}
		}
		return true;
	}

	private function doTheAction($subscriber){

		$mainModel = Mage::getModel('mailchimp/mailchimp');
		$mainModel->setStore($subscriber->getStoreId());
		if($subscriber->getOldEmail()){
			$mainModel->setEmail($subscriber->getOldEmail());
		}else{
			$mainModel->setEmail(($subscriber->getEmail())? $subscriber->getEmail() : $subscriber->getSubscriberEmail());
		}
		$mainModel->setListId($subscriber->getListId());
	    $member = $mainModel->getListMemberInfo();

		$isSubscribed = ($subscriber->getIsAdditionalList() || $subscriber->getIsOnlyUpdate() ||
						($subscriber->getSubscriberStatus() && $subscriber->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED))? true : false ;

		if($member){
			$subscriber->setAction(Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_UNSUBSCRIBE);
			if($member['status'] == 'subscribed'){
				if($isSubscribed && !$subscriber->getToUnsubscribe()){
					$subscriber->setAction(Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_UPDATE);
				}
			}else{
				if($isSubscribed  || (bool)$this->getSubscribeConfig('double_optin',$subscriber->getStoreId())){
					$subscriber->setAction(Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_RESUBSCRIBE);
					if($subscriber->getisSilentUpdate()){
						$subscriber->setAction(Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_SILENTUPDATE);
					}
				}else{
					$subscriber->setAction(null);
				}
			}
		}else{
			if($isSubscribed){
				$subscriber->setAction(Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_SUBSCRIBE);
			}elseif($subscriber->getSubscriberStatus() && $subscriber->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE){
				$subscriber->setAction(Ebizmarts_Mailchimp_Model_Mailchimp::ACTION_SILENTSUBSCRIBE);
			}
		}

		if($subscriber->getAction()){
			$subscriber->setRegistered($this->isMailchimpSubscribed($subscriber));
			$memberId = ($subscriber->getRegistered()->getData())? $subscriber->getRegistered()->getMemberId() : $subscriber->getEmail();
			$subscriber->setIdentifier($memberId);
			$mainModel->setCustomer($subscriber);
			$mainModel->mainAction();
		}
		return true;
	}

	public function webHookFilter($data){

		$currentStore = Mage::app()->getDefaultStoreView()->getStoreId();
		if($this->mailChimpEnabled($currentStore)){
			$lists = $this->getAvailablelists($currentStore);
			$lists = (is_array($lists))? $lists : array();
			$generalList = $this->getGeneralConfig('general',$currentStore);
			$lists[$generalList] = $generalList;

			if(array_key_exists($data['data']['list_id'],$lists)){

				$customer = Mage::getSingleton('mailchimp/subscripter')->prepareCustomer($data);

				if($customer->getMailchimpproId() || $customer->getAction() == 'subscribe'){
					Mage::getSingleton('mailchimp/subscripter')->registerInfo($customer);
				}

				$email = (isset($data['data']['email']))? $data['data']['email'] : $data['data']['old_email'] ;

				if($customer->getAction() == 'subscribe' && !$this->isSubscribed($email) && $data['data']['list_id'] == $generalList){
					Mage::getModel('mailchimp/subscriber')->subscribe($email,true);
				}elseif(($customer->getAction() == 'unsubscribe' || $customer->getAction() == 'cleaned' ) && $data['data']['list_id'] == $generalList && $this->isSubscribed($email)){
					Mage::getModel('newsletter/subscriber')->loadByEmail($email)->unsubscribe();
				}
			}
		}
	}

	public function getCtemplatesCollection(){

		$collection = Mage::getModel('mailchimp/mysql4_helper_collection');

        $allTemplates = Mage::getSingleton('mailchimp/mailchimp')->getCtemplates();
        if(is_array($allTemplates) && count($allTemplates)){
			foreach($allTemplates as $source=>$templates){
        		if(is_array($templates) && count($templates)){
        			foreach($templates as $template){
						$item = new Varien_Object;
			          	$item->addData($template);
			          	$item->setTid($source);
			          	$collection->addItem($item);
        			}
	          	}
			}
        }
        return $collection;
	}

    public function getPageUrl($pageId = null, $storeId){

        $page = Mage::getSingleton('cms/page');
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $page->setStoreId($storeId);
            if (!$page->load($pageId)) {
                return null;
            }
        }

        if (!$page->getId()) {
            return null;
        }
		Mage::app("default")->setCurrentStore($storeId);
        return Mage::getUrl($page->getIdentifier());
    }

 	public function addException($e){

		$currentStore = Mage::app()->getStore()->getStoreId();

		foreach(explode("\n", $e->getMessage()) as $message) {
    		if ($currentStore == 0){
            	Mage::getSingleton('adminhtml/session')->addError($this->__('Mailchimp General Error: ').$message);
    		}else{
    			Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription, please try again later.'));
    		}
			$message = 'Exception code: '.$e->getCode().' ||| Exception message: '.$e->getMessage();
			if($e->getTraceAsString()) $message .= ' ||| Trace: '.$e->getTraceAsString();
        	Mage::log($message, Zend_Log::DEBUG, 'mailChimp_Exceptions.log');
        }
        return $this;
	}
}
?>
