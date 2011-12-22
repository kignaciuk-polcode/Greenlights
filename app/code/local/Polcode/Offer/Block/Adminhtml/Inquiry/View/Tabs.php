<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function getInquiry()
    {
        if ($this->hasOrder()) {
            return $this->getData('offer');
        }
        if (Mage::registry('current_offer')) {
            return Mage::registry('current_offer');
        }
        if (Mage::registry('offer')) {
            return Mage::registry('offer');
        }
        Mage::throwException(Mage::helper('offer')->__('Cannot get the inquiry instance.'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('offer_inquiry_view_tabs');
        $this->setDestElementId('offer_inquiry_view');
        $this->setTitle(Mage::helper('offer')->__('Inquiry View'));
    }

}