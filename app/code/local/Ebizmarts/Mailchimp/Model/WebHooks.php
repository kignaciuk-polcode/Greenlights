<?php

class Ebizmarts_Mailchimp_Model_WebHooks extends Ebizmarts_Mailchimp_Model_MCAPI{

	protected $_actions = array();
	protected $_sources = array();
	protected $_response= array();

	private function setWebHookParams($list = ''){

		if(is_array($list)){
			$this->_actions['subscribe'] = (bool)$list['subscribe'];
			$this->_actions['unsubscribe'] = (bool)$list['unsubscribe'];
			$this->_actions['profile'] = (bool)$list['profile'];
			$this->_actions['cleaned'] = (bool)$list['cleaned'];
			$this->_actions['upemail'] = (bool)$list['upemail'];
			$this->_sources['user'] = (bool)$list['user'];
			$this->_sources['admin'] = (bool)$list['admin'];
			$this->_sources['api'] = (bool)$list['api'];
		}
		return $this;
	}

	public function mainWebHooksAction($listId){

		$helper = Mage::helper('mailchimp');
		$apikey = $helper->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);
		$this->setWebHookUrl(Mage::getStoreConfig('web/unsecure/base_url',Mage::app()->getDefaultStoreView()->getStoreId()).'mailchimp/capture');

		if(is_array($listId)){
			$list = array();
			foreach($listId as $val){
				$list[substr($val,0,strpos($val,'='))] = substr($val,strpos($val,'=')+1,strlen($val));
			}
			$this->setWebHookParams($list);

			$this->_response = $this->listWebhooks($list['list_id']);
			if ($this->errorCode){
				$this->setErrorOutput();
				return false;
			}

			if($this->getHook()){
				$this->listWebhookDel($list['list_id'],$this->getWebHookUrl());
				if ($this->errorCode){
					$this->setErrorOutput();
					return false;
				}
			}
			$this->listWebhookAdd($list['list_id'],$this->getWebHookUrl(),$this->_actions,$this->_sources);
			if ($this->errorCode){
				$this->setErrorOutput();
				return false;
			}
		}else{
			$this->_response = $this->listWebhooks($listId);
			if ($this->errorCode){
				$this->setErrorOutput();
				return false;
			}
		}

		if($hook = $this->getHook()){
			return $hook;
		}
		return true;
	}

	private function getHook(){

		if(count($this->_response)){
			foreach($this->_response as $hook){
				if($hook['url'] == $this->getWebHookUrl()){
					return $hook;
				}
			}
		}
		return false;
	}

	private function setErrorOutput(){

		$this->setCode($this->errorCode);
		$this->setMessage($this->errorMessage);

		Mage::helper('mailchimp')->addException($this);

		unset($this->errorCode, $this->errorMessage);

		return $this;
	}

	public function updateController(){

		$fileName = 'WebHooksCapture.php';
		if (is_file(Mage::getBaseDir() . DS . $fileName)) {
			$url = Mage::getStoreConfig('web/unsecure/base_url',Mage::app()->getDefaultStoreView()->getStoreId());
			$this->setWebHookUrl($url.$fileName);

			$lists = Mage::getSingleton('mailchimp/mailchimp')->getLists();

			$helper = Mage::helper('mailchimp');
			$apikey = $helper->getApiKey();
			if(!$apikey){
				return false;
			}

			$this->MCAPI($apikey);

			foreach($lists['data'] as $list){
				$this->_response = $this->listWebhooks($list['id']);
				if ($this->errorCode){
					$this->setErrorOutput();
					return false;
				}
				if(count($this->_response)){
					foreach($this->_response as $hook){
						if($hook['url'] == $this->getWebHookUrl()){
							$this->listWebhookDel($list['id'],$this->getWebHookUrl());
							if ($this->errorCode){
								$this->setErrorOutput();
								return false;
							}
							$this->listWebhookAdd($list['id'],$url.'mailchimp/capture',$hook['actions'],$hook['sources']);
							if ($this->errorCode){
								$this->setErrorOutput();
								return false;
							}
						}
					}
				}
			}
			$this->setWebHookUrl($url.'mailchimp/capture');
		    try {
		        $ioProxy = new Varien_Io_File();
		        $ioProxy->open(array('path'=>Mage::getBaseDir()));
		        $ioProxy->rm($fileName);
		    }catch (Exception $e) {
		        Mage::helper('mailchimp')->addException($e);
		    }
	    }
	    return true;
	}

}