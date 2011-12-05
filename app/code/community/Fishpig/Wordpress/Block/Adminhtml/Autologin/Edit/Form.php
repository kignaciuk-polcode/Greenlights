<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Autologin_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$adminUser = Mage::registry('wordpress_admin_user');
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array('id' => ($adminUser) ? $adminUser->getId() : null)),
			'method' => 'post'
		));
	
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}
