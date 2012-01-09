<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Autologin_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		
		$this->setForm($form);

		$fieldset = $form->addFieldset('autologin_form', array('legend'=>'&nbsp;'));

		$fieldset->addField('username', 'text', array(
			'label'     	=> 'Username',
			'class'     	=> 'required-entry',
			'required'  => true,
			'name'      => 'username',
		));
		
		$fieldset->addField('password', 'password', array(
			'label'     	=> 'Password',
			'class'     	=> 'required-entry',
			'required'  => true,
			'name'      => 'password',
		));

		$configUrl = $this->getUrl('adminhtml/system_config/edit/section/wordpress');
		
		$text = new Varien_Data_Form_Element_Note();
		$text->setText('
			<input type="hidden" name="redirect_to" value="'.$this->_getRedirectTo().'"/>
			<p style="font-weight:bold; padding:10px 5px;">
				Before you attempt to use this feature it is recommended that you pass all integration tests in the <a href="'.$configUrl.'" style="text-decoration:none;">Magento/WordPress configuration</a> section
			</p>
		');
		
		$fieldset->addElement($text);
		
		if (Mage::registry('wordpress_admin_user')) {
			$form->setValues(Mage::registry('wordpress_admin_user')->getData());
		}

		return parent::_prepareForm();
	}
	
	protected function _getRedirectTo()
	{
		return Mage::app()->getRequest()->getParam('redirect_to');
	}
}
