<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ctemplate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

  public function __construct(){

      parent::__construct();
      $this->setId('ctemplate_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mailchimp')->__('Campaign Template'));
  }

  protected function _beforeToHtml(){

      $this->addTab('form_info', array(
          'label'     => Mage::helper('mailchimp')->__('Info'),
          'title'     => Mage::helper('mailchimp')->__('Info'),
          'content'   => $this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate_edit_tab_info')->toHtml(),
      ));
      $this->addTab('form_sourcepreview', array(
          'label'     => Mage::helper('mailchimp')->__(Mage::registry('ctemplate_data')? 'Source and preview' : 'Source'),
          'title'     => Mage::helper('mailchimp')->__(Mage::registry('ctemplate_data')? 'Source and preview' : 'Source'),
          'content'   => $this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate_edit_tab_sourcepreview')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }

}