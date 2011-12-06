<?php

class Ebizmarts_Mailchimp_Adminhtml_WebHooksController extends Mage_Adminhtml_Controller_Action{

	public function indexAction() {

    	$this->loadLayout()
    		 ->_setActiveMenu('newsletter')
        	 ->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_webHooks'))
          	 ->renderLayout();
	}

	public function newAction(){

		$mod = 0;
     	foreach($this->getRequest()->getPost() as $list){
     		$items = explode('&',$list);
     		if(is_array($items) && count($items) > 1){
     			if(Mage::getSingleton('mailchimp/webHooks')->mainWebHooksAction($items)) $mod++;
     		}
    	}

		if($mod > 0){
			$text = ($mod == 1)? Mage::helper('mailchimp')->__('Total of %d list was updated.', $mod) : Mage::helper('mailchimp')->__('Total of %d list(s) were updated.', $mod);
			Mage::getSingleton('adminhtml/session')->addSuccess($text);
		}
	}

}