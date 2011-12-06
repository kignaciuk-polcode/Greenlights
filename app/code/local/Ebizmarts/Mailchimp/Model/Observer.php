<?php
class Ebizmarts_Mailchimp_Model_Observer extends Mage_Core_Model_Abstract {

    public function subscribeObserver($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();

		if(isset($params['email'])){
			$params['is_general'] = true;
			Mage::helper('mailchimp')->preFilter($params['email'],$params);
		}

        return $this;
    }

    public function customerObserver($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();

		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$email = $customer->getEmail();

		if(isset($email)){
			Mage::helper('mailchimp')->preFilter($email,$params);
		}
        return $this;
    }

	public function registerObserver($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();

		if(isset($params['is_subscribed']) || isset($params['subscribe_newsletter']) ||
			(bool)Mage::helper('mailchimp')->getSubscribeConfig('forece_checkout',Mage::app()->getStore()->getStoreId())){

			$email = (isset($params['billing']['email']))? $params['billing']['email'] : Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			$params['is_general'] = (!isset($params['is_general']))? (int)1 : $params['is_general'];

			if($email){
				Mage::getModel('newsletter/subscriber')->subscribe($email,true);
				Mage::helper('mailchimp')->preFilter($email,$params);
			}
		}
        return $this;
    }

    public function updateNewObserver($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		$cusSession = Mage::getSingleton('customer/session')->getCustomer();

		$customer = ($cusSession->getEmail())? $cusSession : $observer->getCustomer();

		$email = (isset($params['email']))? $params['email'] : $customer->getEmail() ;
		if(Mage::registry('oldEmail')){
			$params['oldEmail'] = Mage::registry('oldEmail');
		}

		if($email){
			$params['onlyUpdate'] = true;
			Mage::helper('mailchimp')->preFilter($email,$params);
		}

        return $this;
    }

	public function updateNewRegister($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		if(isset($params['email'])){
			$cusSession = Mage::getSingleton('customer/session')->getCustomer();
			$customer = Mage::getSingleton('customer/customer')->load($cusSession->getEntityId());
			if($params['email'] != $customer->getEmail()){
				Mage::register('oldEmail', $customer->getEmail());
			}
		}

		return $this;
	}

	public function adminCustomerSave($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		if(!isset($params['id']) && isset($params['customer_id'])) $params['id'] = $params['customer_id'];

		if(isset($params['id'])){
			Mage::helper('mailchimp')->preAdminFilter($params);
		}

		return $this;
	}

	public function adminCustomerRegister($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();

		if(isset($params['customer_id'])){
			$customer = Mage::getSingleton('customer/customer')->load($params['customer_id']);
			if($params['account']['email'] != $customer->getEmail()){
				Mage::register('oldEmail', $customer->getEmail());
			}
		}

		return $this;
	}

	public function adminCustomerDelete($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		if(isset($params['customer'])  || isset($params['id'])){
			if(!isset($params['customer'])){
				$params['customer'] = array('0'=>$params['id']);
			}
			Mage::getModel('mailchimp/bulkSynchro')->adminMassDelete($params);
		}

		return $this;
	}

	public function adminCustomerSubscribe($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		if(isset($params['customer'])  || isset($params['id'])){
			Mage::getModel('mailchimp/bulkSynchro')->adminMassSubscribe($params);
		}

		return $this;
	}

	public function newsCustomerDelete($observer){

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		if(isset($params['subscriber']) && count($params['subscriber'])){
			Mage::getModel('mailchimp/bulkSynchro')->newsMassDelete($params);
		}

		return $this;
	}

 	public function ecomm360($observer){

        $order = $observer->getEvent()->getOrder();
		Mage::getSingleton('mailchimp/ecomm360')->runEcomm360($order);
        return $this;
    }

	public function registerMe($observer){

		Mage::getSingleton('mailchimp/ecomm360')->registerMe();
        return $this;
    }

}
?>
