<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Support_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('support_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle($this->_wrapEnquiryType('Support Ticket'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('dashboard', array(
          'label'    => $this->_wrapEnquiryType('Support Ticket'),
          'title'    => 'Enquiry Type',
          'content'   => $this->getLayout()->createBlock('wordpress/adminhtml_template')->setTemplate('wordpress/support/index.phtml')->toHtml(),
      ));

      $this->addTab('content', array(
          'label'    => $this->__('Details'),
          'title'    => $this->__('Details'),
          'content'   => $this->getLayout()->createBlock('wordpress/adminhtml_support_edit_tab_requestForm')->toHtml(),
      ));
      
      $this->addTab('debug', array(
          'label'    => $this->__('System Information'),
          'title'    => '- '.$this->__('System Information'),
          'content'   => $this->getLayout()->createBlock('wordpress/adminhtml_template')->setTemplate('wordpress/support/tab/debug-info.phtml')->toHtml(),
      ));  
      
      $this->addTab('log_files', array(
          'label'    => $this->__('Log Files'), 
          'title'    => $this->__('Log Files'),
          'content'   => $this->getLayout()->createBlock('wordpress/adminhtml_template')->setTemplate('wordpress/support/tab/logs.phtml')->toHtml(),
      ));  
      
      $this->addTab('complete', array(
          'label'    => $this->__('Send Your ').$this->_wrapEnquiryType('Support Ticket'),
          'title'    => $this->__('Send'),
          'content'   => $this->getLayout()->createBlock('wordpress/adminhtml_template')->setTemplate('wordpress/support/tab/complete.phtml')->toHtml(),
      ));   

      return parent::_beforeToHtml();
  }
  
	protected function _wrapEnquiryType($text)
	{
		return '<span class="enquirytype" style="background:none; display:inline;padding:0;">'.$this->__($text).'</span>';
	}  
}
