<?php

class Polcode_Offer_Block_Inquiry_View extends Mage_Catalog_Block_Product_Abstract {

    public function __construct()
    {
        parent::__construct();

        $items = Mage::getResourceModel('offer/inquiry_item_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('inquiry_id', $this->getRequest()->getParam('inquiry_id'))
            ->setOrder('added_at', 'desc')
        ;
        
        $this->setInquiryItems($items);
        
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Inquiry # %s', $this->getRequest()->getParam('inquiry_id')));
        }
 
    }
    
    protected function _getHelper()
    {
        return Mage::helper('offer/inquiry');
    }    
    
    protected function _getInquiry()
    {
        return $this->_getHelper()->getInquiry();
    }    
    
    public function hasInquiryItems()
    {
        return $this->getInquiryItemsCount() > 0;
    }    

    
    public function getInquiryItemsCount()
    {
        return $this->_getInquiry()->getItemsCount();
    }    
    
    
    public function getProductUrl($item, $additional = array())
    {
        if ($item instanceof Mage_Catalog_Model_Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $buyRequest = $item->getBuyRequest();
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->load($config['product_id']);
            }
        }
        return parent::getProductUrl($product, $additional);
    }
    
    public function getItemConfigureUrl($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $id = $product->getInquiryItemId();
        } else {
            $id = $product->getId();
        }
        $params = array('id' => $id);

        return $this->getUrl('*/*/configure/', $params);
    }
    
    public function getItemRemoveUrl($item)
    {       
        return Mage::helper('offer/inquiry')->getRemoveUrl($item);
    }    
    
    public function getAddToCartQty(Polcode_Offer_Model_Inquiry_Item $item)
    {      
        $qty = $item->getProductQty();
        return $qty ? $qty : 1;
    }   
    
    public function getRealInquiryId()
    {
        return sprintf("%08d",$this->_getHelper()->getInquiry()->getId());
    }
    
    public function isInquirySubmitted()
    {
       return $this->_getInquiry()->isSubmitted();
    }
    
    
     
}