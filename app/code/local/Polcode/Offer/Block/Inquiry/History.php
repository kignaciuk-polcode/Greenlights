<?php

class Polcode_Offer_Block_Inquiry_History extends Mage_Core_Block_Template {

    public function __construct()
    {
        parent::__construct();

        $inquiries = Mage::getResourceModel('offer/inquiry_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('updated_at', 'desc')
        ;
        
        $this->setInquiries($inquiries);
        
    }
    
    /**
     * Preparing global layout
     *
     * @return Polcode_Offer_Block_Inquiry_History
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('My Offer Inquiry'));
        }
    }
    
    public function getViewUrl($inquiry)
    {
        return $this->getUrl('*/*/view', array('inquiry_id' => $inquiry->getId()));
    }    
    
    public function getSendUrl($inquiry)
    {
        return $this->getUrl('*/*/send', array('inquiry_id' => $inquiry->getId()));
    }     
    
}
