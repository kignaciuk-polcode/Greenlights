<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_View_Items extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block'));
        }
        //$this->setOrder($this->getParentBlock()->getOrder());
        parent::_beforeToHtml();
    }

    public function getItemsCollection()
    {       
        return $this->getInquiry()->getItemCollection();
    }
    
    public function getInquiry()
    {
        if ($this->hasInquiry()) {
            return $this->getData('inquiry');
        }
        if (Mage::registry('current_inquiry')) {
            return Mage::registry('current_inquiry');
        }
        if (Mage::registry('inquiry')) {
            return Mage::registry('inquiry');
        }
        if ($this->getItem()->getInquiry())
        {
            return $this->getItem()->getInquiry();
        }

        Mage::throwException(Mage::helper('offer')->__('Cannot get inquiry instance'));
    } 
    
    
    public function usedCustomPriceForItem($item)
    {
        return $item->hasCustomPrice();
    }    
    
    public function getOriginalEditablePrice($item)
    {
        if ($item->hasCustomPrice()) {
            $result = $item->getCustomPrice()*1;
        } else {
            $result = Mage::getModel('catalog/product')->load($item->getProductId())->getPrice()*1;            
        }
        return $result;
    }    
    
    
    
    
    
}
