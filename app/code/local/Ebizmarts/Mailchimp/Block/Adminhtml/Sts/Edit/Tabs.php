<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Sts_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

	public function __construct(){
		parent::__construct();
      	$this->setId('stsTabs');
      	$this->setDestElementId('edit_form');
      	$this->setTitle(Mage::helper('mailchimp')->__('Email to authorize'));
  	}

	protected function _beforeToHtml(){

		$this->addTab('form_section', array(
								          'label'     => Mage::helper('mailchimp')->__('Email'),
								          'title'     => Mage::helper('mailchimp')->__('Email'),
								          'content'   => $this->getLayout()->createBlock('mailchimp/adminhtml_sts_edit_tab_form')->toHtml(),
								      ));
		return parent::_beforeToHtml();
  	}
}
