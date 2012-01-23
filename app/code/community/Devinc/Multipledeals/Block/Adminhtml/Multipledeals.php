<?php
class Devinc_Multipledeals_Block_Adminhtml_Multipledeals extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_multipledeals';
    $this->_blockGroup = 'multipledeals';
    $this->_headerText = Mage::helper('multipledeals')->__('Deal Manager');
    $this->_addButtonLabel = Mage::helper('multipledeals')->__('Add Deal');
    
	Mage::getModel('multipledeals/multipledeals')->refreshDeals();
	
	parent::__construct();
	
  }
}