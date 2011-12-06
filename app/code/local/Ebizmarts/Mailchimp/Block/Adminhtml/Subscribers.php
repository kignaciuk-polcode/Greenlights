<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Subscribers extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){

    	$this->_controller = 'adminhtml_subscribers';
        $this->_blockGroup = 'mailchimp';
        $this->_headerText = Mage::helper('mailchimp')->__('MailChimp - Synchronized subscribers for all lists');
		parent::__construct();
		$this->_removeButton('add');

      }

  }