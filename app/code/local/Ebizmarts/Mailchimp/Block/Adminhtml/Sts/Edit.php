<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Sts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{

    public function __construct(){

        parent::__construct();
        $this->_controller = 'adminhtml_sts';
        $this->_blockGroup = 'mailchimp';
        $helper = Mage::helper('mailchimp');

        $this->_objectId = 'emailadress';
        $this->_updateButton('save', 'label', $helper->__('Save Email'));
        $this->_removeButton('reset');
    }

    public function getHeaderText(){

        return Mage::helper('mailchimp')->__('Add Addresses');
    }
}