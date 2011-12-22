<?php

class Polcode_Offer_Helper_Inquiry extends Mage_Core_Helper_Abstract {

    const XML_PATH_INQUIRY_LINK_USE_QTY = 'offer/inquiry_link/use_qty';

    const XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';    
    
    protected $_inquiryItemCollection = null;
    protected $_inquiry = null;
    
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }    
    
    protected function _isCustomerLogIn()
    {
        return $this->_getCustomerSession()->isLoggedIn();
    }    
    
    protected function _getUrlStore($item)
    {
        $storeId = null;
        $product = null;
        if ($item instanceof Polcode_Offer_Model_Inquiry_Item) {
            $product = $item->getProduct();
        } elseif ($item instanceof Mage_Catalog_Model_Product) {
            $product = $item;
        }
        if ($product) {
            if ($product->isVisibleInSiteVisibility()) {
                $storeId = $product->getStoreId();
            }
            else if ($product->hasUrlDataObject()) {
                $storeId = $product->getUrlDataObject()->getStoreId();
            }
        }
        return Mage::app()->getStore($storeId);
    }    
    
    public function canSubmitt($inquiry){
        
        $result = false;
        
        if(!$inquiry->getSubmitted()){        
            $result = true;
        }
        
        return $result; 
    }
    
 
    

    public function isAllow()
    {
        // @TODO add logic to the isAllow() function
        // 
//        if ($this->isModuleOutputEnabled() && Mage::getStoreConfig('offer/general/active')) {
//            return true;
//        }
//        return false;
        return true;
    }    
    
    public function getInquiryItemCollection()
    {
        if (is_null($this->_inquiryItemCollection)) {
            $this->_inquiryItemCollection = $this->getInquiry()
                ->getItemCollection();
        }
        return $this->_inquiryItemCollection;
    }    
    
    
    public function calculate()
    {
        $session = $this->_getCustomerSession();
        $count = 0;
        if ($this->_isCustomerLogIn()) {
                                  
            $collection = $this->getInquiryItemCollection()->setInStockFilter(true);
            if (Mage::getStoreConfig(self::XML_PATH_INQUIRY_LINK_USE_QTY)) {
                $count = $collection->getItemsQty();
            } else {
                $count = $collection->getSize();
            }
            $session->setWishlistDisplayType(Mage::getStoreConfig(self::XML_PATH_INQUIRY_LINK_USE_QTY));
            $session->setDisplayOutOfStockProducts(
                Mage::getStoreConfig(self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK)
            );
        }
        $session->setWishlistItemCount($count);
        Mage::dispatchEvent('inquiry_items_renewed');
        return $this;
    }
    
    public function getAddToCartUrl($item)
    {               
        $urlParamName = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $continueUrl  = Mage::helper('core')->urlEncode(
            Mage::getUrl('*/*/*', array(
                '_current'      => true,
                '_use_rewrite'  => true,
                '_store_to_url' => true,
            ))
        );

        $urlParamName = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $params = array(
            'item' => is_string($item) ? $item : $item->getInquiryItemId(),
            $urlParamName => $continueUrl
        );
        return $this->_getUrlStore($item)->getUrl('offer/inquiry/cart', $params);
    }
    
    public function getUpdateUrl($item)
    {
        $itemId = null;
        if ($item instanceof Mage_Catalog_Model_Product) {
            $itemId = $item->getInquiryItemId();
        }
        if ($item instanceof Polcode_Offer_Model_Inquiry_Item) {
            $itemId = $item->getId();
        }
        
        if ($itemId) {
            $params['id'] = $itemId;
            return $this->_getUrlStore($item)->getUrl('offer/inquiry/updateItemOptions', $params);
        }

        return false;
    }
    
    public function getRemoveUrl($item)
    {       
        return $this->_getUrl('offer/inquiry/remove',
            array('item' => $item->getInquiryItemId())
        );
    }    
    
    public function getInquiry()
    {
        if (is_null($this->_inquiry)) {

            if (Mage::registry('inquiry')) {
                $this->_inquiry = Mage::registry('inquiry');
            }
            else {
                $this->_inquiry = Mage::getModel('offer/inquiry');
                if ($this->_getCustomerSession()->isLoggedIn()) {
                    $this->_inquiry->loadByCustomer($this->_getCustomerSession()->getCustomer());
                }
            }
        }
               
        return $this->_inquiry;
    }
      
           
}