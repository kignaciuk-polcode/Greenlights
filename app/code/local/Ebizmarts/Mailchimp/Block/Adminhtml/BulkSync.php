<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_BulkSync extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){

    	$this->_controller = 'adminhtml_bulkSync';
        $this->_blockGroup = 'mailchimp';
        $this->_headerText = Mage::helper('mailchimp')->__('MailChimp - Bulk Synchronization');
		parent::__construct();
		$this->_removeButton('add');

      }

      protected function _prepareLayout(){

      	parent::_prepareLayout();
      	if(!$this->getRequest()->isXmlHttpRequest()){
      		$this->getLayout()->getBlock('head')->addItem('skin_js', 'mailchimp/MailChimp.js');
      		$this->getLayout()->getBlock('head')->addItem('skin_css', 'mailchimp/MailChimp.css');
      	}
      }
  }