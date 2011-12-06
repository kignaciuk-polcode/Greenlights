<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Sts extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){

	    $this->_controller = 'adminhtml_sts';
	    $this->_blockGroup = 'mailchimp';
	    $this->_headerText = Mage::helper('mailchimp')->__('MailChimp - STS');
	    parent::__construct();
  	}

}
