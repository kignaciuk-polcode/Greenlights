<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Sts_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{

	protected function _prepareForm(){

		$form = new Varien_Data_Form();
	   	$this->setForm($form);
	   	$fieldset = $form->addFieldset('stsForm', array('legend'=>Mage::helper('mailchimp')->__('Item information')));
		$fieldset->addField('emailaddress', 'text', array(
	          'label'     => Mage::helper('mailchimp')->__('Email Address'),
	          'class'     => 'required-entry',
	          'required'  => true,
	          'style'     => 'width:200px;',
	          'name'      => 'emailaddress',
	   	));

	   	return parent::_prepareForm();
	}
}