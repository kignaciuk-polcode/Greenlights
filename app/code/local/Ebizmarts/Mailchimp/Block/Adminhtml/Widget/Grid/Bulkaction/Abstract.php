<?php

abstract class Ebizmarts_Mailchimp_Block_Adminhtml_Widget_Grid_Bulkaction_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract{

 	public function __construct(){
        parent::__construct();
        $this->setTemplate('mailchimp/widget/grid/bulkaction.phtml');
        $this->setErrorText(Mage::helper('catalog')->jsQuoteEscape(Mage::helper('mailchimp')->__('Please complete all fields.')));
    }

    public function getApplyButtonHtml(){
        return $this->getButtonHtml($this->__('Create'), "formSubmit('".$this->getHtmlId()."','".$this->getErrorText()."','".$this->getFormActionUrl()."');");
    }

    protected function getFormActionUrl(){
        return $this->getUrl('mailchimp/adminhtml_bulkSync/new', array('_secure' => true));
    }

	protected function getErrorListText(){
        return Mage::helper('mailchimp')->__('You must choose the way and the list to continue.');
    }

}
?>
