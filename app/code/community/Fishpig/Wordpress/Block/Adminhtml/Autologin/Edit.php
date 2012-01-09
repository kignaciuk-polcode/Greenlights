<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Autologin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_objectId 	= 'id';
		$this->_blockGroup 	= 'wordpress';
		$this->_controller 	= 'adminhtml_autologin';
		$this->_buttons 	= array();

		$this->_addButton('save', array(
			'label'     => Mage::helper('adminhtml')->__('Save'),
			'onclick'   => 'editForm.submit();',
			'class'     => 'save',
		), 1);
	}

	public function getHeaderText()
	{
		return $this->__('1-Click WordPress Login');
	}
}
