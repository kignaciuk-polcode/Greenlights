<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_View_Info extends Polcode_Offer_Block_Adminhtml_Inquiry_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setInquiry($this->getParentBlock()->getInquiry());

//        foreach ($this->getParentBlock()->getOrderInfoData() as $k => $v) {
//            $this->setDataUsingMethod($k, $v);
//        }

        parent::_beforeToHtml();
    }
    
    public function getCustomerViewUrl()
    {
        if (!$this->getInquiry()->getCustomerId()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getInquiry()->getCustomerId()));
    }    
    
    public function getCustomerGroupName()
    {
        if ($this->getInquiry()) {
                                 
            return Mage::getModel('customer/group')
                    ->load(Mage::getModel('customer/customer')
                            ->load($this->getInquiry()
                                    ->getCustomerId())
                                    ->getGroupId())
                            ->getCode();
        }
        return null;
    }
    
    
 
}
