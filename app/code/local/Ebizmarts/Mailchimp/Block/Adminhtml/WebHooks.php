<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_WebHooks extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){

    	$this->_controller = 'adminhtml_webHooks';
        $this->_blockGroup = 'mailchimp';
        $this->_headerText = Mage::helper('mailchimp')->__('MailChimp - WebHooks');
		parent::__construct();
    	$this->_addButton('add', array(
			            'label'     => 'WebHooks Synchronization',
			            'onclick'   => 'submitMyHooks(\'' . Mage::helper('adminhtml')->getUrl('mailchimp/adminhtml_webHooks/new') . '\')',
			            'class'     => 'add'
    					));
      }

      protected function _prepareLayout(){

      	parent::_prepareLayout();
      	if(!$this->getRequest()->isXmlHttpRequest()){
      		$this->getLayout()->getBlock('head')->addItem('skin_js', 'mailchimp/MailChimp.js');
      	}
      }
  }