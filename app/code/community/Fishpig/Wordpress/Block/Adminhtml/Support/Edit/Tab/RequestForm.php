<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Support_Edit_Tab_RequestForm extends Fishpig_Wordpress_Block_Adminhtml_Support_Edit_Tab_Form_Abstract
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

		$this->setForm($form);
		$fieldset = $form->addFieldset('question_form', array('legend'=>'<span class="enquirytype">Support Ticket</span> Details'));
	
		$fieldset->addField('sender_name', 'text', array(
			'label' => 'Your Name',
			'name' => 'sender_name',
			'required' => true,
		));
		
		$fieldset->addField('sender_email', 'text', array(
			'label' => 'Email Address',
			'name' => 'sender_email',
			'class' => 'validate-email',
			'required' => true,
		));
		
		$fieldset->addField('enquiry_type', 'select', array(
			'label' => 'Enquiry Type',
			'name' => 'enquiry_type',
			'required' => 'true',
			'values' => $this->_getEnquiryTypeOptions(),
		));

		$fieldset->addField('subject', 'text', array(
			'label' => 'Subject',
			'name' => 'subject',
			'required' => true,
		));
		
		$fieldset->addField('content', 'textarea', array(
			'label' => 'Details',
			'name' => 'content',
			'required' => true,
		));

		$fieldset->addField('buttons', 'note', array(
			'text' => '<div style="height:1%;overflow:hidden;"><button type="button" class="scalable back left support-previous" id="content_previous"><span>' . $this->__('Previous') . '</span></button><button type="button" class="scalable add right support-next" id="content_next"><span>' . $this->__('Next') . '</span></button></div>',
		));

		if ($supportData = Mage::getSingleton('adminhtml/session')->getSupportData()) {
			$form->setValues($supportData);
		}
		
		return parent::_prepareForm();
	}


	protected function _getEnquiryTypeOptions()
	{
		$options = array();
		
		foreach(array($this->__('Support Ticket'), $this->__('Bug Report')) as $value) {
			$options[] = array('value' => $value, 'label' => $this->__($value));
		}
		
		return $options;
	}
}
