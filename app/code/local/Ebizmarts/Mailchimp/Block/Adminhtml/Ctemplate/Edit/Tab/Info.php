<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ctemplate_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form{

	protected function _prepareForm(){

    	$form = new Varien_Data_Form();
      	$this->setForm($form);
      	$fieldset = $form->addFieldset('form_info', array('legend'=>Mage::helper('mailchimp')->__('Campaign Template Info')));

		if(Mage::registry('ctemplate_data')){
			$fieldset->addField('id', 'label', array(
	          	'label'     => Mage::helper('mailchimp')->__('Template Id'),
	          	'name'      => 'id',
	      	));
			$fieldset->addField('layout', 'label', array(
	          	'label'     => Mage::helper('mailchimp')->__('Layout'),
	          	'name'      => 'layout',
	      	));
			$fieldset->addField('tid', 'label', array(
	          	'label'     => Mage::helper('mailchimp')->__('Type Id'),
	          	'name'      => 'tid',
	      	));
			$fieldset->addField('date_created', 'label', array(
	          	'label'     => Mage::helper('mailchimp')->__('Date Created'),
	          	'name'      => 'date_created',
	      	));
			$fieldset->addField('active', 'label', array(
	          	'label'     => Mage::helper('mailchimp')->__('Active'),
	          	'name'      => 'active',
	      	));
			$fieldset->addField('previewlink_text', 'link', array(
	        	'title'     => Mage::helper('mailchimp')->__('Preview saved template on popup'),
	          	'name'      => 'previewlink_text',
	          	'class'     => 'link',
	          	'onclick'   => 'previewMe(\'original_source\')'
	      	));
		}

		$fieldset->addField('name', 'text', array(
        	'label'     => Mage::helper('mailchimp')->__('Name'),
          	'name'      => 'name',
          	'class'     => 'required-entry',
          	'required'  => true,
      	));

		if(Mage::getSingleton('adminhtml/session')->getCtemplateData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getCtemplateData());
	        Mage::getSingleton('adminhtml/session')->setCtemplateData(null);
		}elseif(Mage::registry('ctemplate_data')){
			$form->setValues(Mage::registry('ctemplate_data'));
	  	}

	    return parent::_prepareForm();
  	}

}