<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ecomm360 extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){

    	$this->_controller = 'adminhtml_ecomm360';
        $this->_blockGroup = 'mailchimp';
        $this->_headerText = Mage::helper('mailchimp')->__('MailChimp - Ecommerce 360');
		parent::__construct();
		$this->_removeButton('add');

      }

  }