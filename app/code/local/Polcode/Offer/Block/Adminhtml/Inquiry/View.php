<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_View extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {



        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_inquiry';
        $this->_mode = 'view';
        $this->_blockGroup = 'offer';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');

        $this->setId('offer_inquiry_view');

        $order = $this->getInquiry();


//        $this->_updateButton('save', 'label', Mage::helper('offer/inquiry')->__('Save Item'));
//        $this->_updateButton('delete', 'label', Mage::helper('offer/inquiry')->__('Delete Item'));
    }

    public function getHeaderText() {

        return Mage::helper('offer')->__('Inquiry # %s | %s', $this->getInquiry()->getId(), $this->formatDate($this->getInquiry()->getUpdatedAt(), 'medium', true));
    }

    public function getInquiry() {
        return Mage::registry('offer_inquiry');
    }

    public function getOrderId() {
        return $this->getInquiry()->getId();
    }

}
