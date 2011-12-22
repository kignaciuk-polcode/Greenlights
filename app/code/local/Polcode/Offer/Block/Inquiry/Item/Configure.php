<?php

class Polcode_Offer_Block_Inquiry_Item_Configure extends Mage_Core_Block_Template
{

    protected function getProduct()
    {
        return Mage::registry('product');
    }

    protected function getInquiryItem()
    {
        return Mage::registry('inquiry_item');
    }

    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = Mage::helper('offer/inquiry')->getAddToCartUrl($this->getInquiryItem());
            $block->setCustomAddToCartUrl($url);
        }

        return parent::_prepareLayout();
    }
}

