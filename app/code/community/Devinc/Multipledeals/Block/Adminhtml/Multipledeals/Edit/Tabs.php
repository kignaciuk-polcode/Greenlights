<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('multipledeals_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('multipledeals')->__('Deal Information'));
  }

  protected function _beforeToHtml()
  {	  
      $this->addTab('form_section', array(
          'label'     => Mage::helper('multipledeals')->__('Deal Settings'),
          'title'     => Mage::helper('multipledeals')->__('Deal Settings'),
          'content'   => $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_tab_form')->toHtml(),
      ));
	  
      $this->addTab('products_section', array(
          'label'     => Mage::helper('multipledeals')->__('Select a Product'),
          'title'     => Mage::helper('multipledeals')->__('Select a Product'),
          'content'   => $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_tab_products')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}