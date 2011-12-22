<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_Abstract extends Mage_Adminhtml_Block_Widget
{

    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('inquiry');
        }
        if (Mage::registry('current_inquiry')) {
            return Mage::registry('current_inquiry');
        }
        if (Mage::registry('inquiry')) {
            return Mage::registry('inquiry');
        }
        Mage::throwException(Mage::helper('offer')->__('Cannot get inquiry instance'));
    }
}
