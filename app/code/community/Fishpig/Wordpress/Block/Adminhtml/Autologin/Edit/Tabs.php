<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Autologin_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('autologin_edit_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('WP Admin Login'));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'    => $this->__('Login Information'),
			'title'    => $this->__('Login Information'),
			'content'   => $this->getLayout()->createBlock('wordpress/adminhtml_autologin_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}