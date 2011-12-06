<?php
class Ebizmarts_Mailchimp_ManageController extends Mage_Core_Controller_Front_Action {

	public function multiSaveAction(){

        if (!$this->_validateFormKey()) {
            return $this->_redirect('newsletter/manage/');
        }

		$params = (Mage::app()->getRequest()->getParams())? Mage::app()->getRequest()->getParams() : array();
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		Mage::helper('mailchimp')->preFilter($customer->getEmail(),$params);

        if (isset($params['list'])) {
        	if(count($params['list']) > 0){
        		Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription(s) has been updated.'));
        	}else{
        		Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been updated.'));
        	}
        } else {
            Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription(s) has been removed.'));
        }

        $this->_redirect('newsletter/manage/');
    }

}
?>
