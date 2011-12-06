<?php
class Ebizmarts_Mailchimp_CaptureController extends Mage_Core_Controller_Front_Action {

	public function indexAction(){

		if($params = $this->getRequest()->getParams()){
			Mage::app("default")->setCurrentStore(Mage::app()->getDefaultStoreView()->getStoreId());
			Mage::helper('mailchimp')->webHookFilter($params);

		}else{
			echo "<div style='width:500px;padding-top:50px;margin:auto;'>" .
				 "<p>This file will capture the <b>MailChimp</b> updates.</p>" .
				 "<p>Thanks for using <b>Ebizmarts</b> modules</p>".
				 "<a href='http://www.ebizmarts.com' style='padding:10px;float:right;'>www.ebizmarts.com</a>" .
				 "</div>";
			 die();
		}
	}
}
?>
