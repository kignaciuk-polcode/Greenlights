<?php
class Polcode_Offer_Model_Inquiry_Item_Option extends Mage_Core_Model_Abstract 
implements Mage_Catalog_Model_Product_Configuration_Item_Option_Interface {

    protected $_item;
    protected $_product;    
    
    protected function _construct() {
        parent::_construct();
        $this->_init('offer/inquiry_item_option');
    }
        
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }    
    
    public function setItem($item)
    {
        $this->setInquiryItemId($item->getId());
        $this->_item = $item;
        return $this;
    }
    
    public function getItem()
    {
        return $this->_item;
    }     
    
    public function setProduct($product)
    {
        $this->setProductId($product->getId());
        $this->_product = $product;
        return $this;
    }    

    public function getProduct()
    {
        return $this->_product;
    }
    
    public function getValue()
    {
        return $this->_getData('value');
    }
      
    protected function _beforeSave()
    {
        if ($this->getItem()) {
            $this->setInquiryItemId($this->getItem()->getId());
        }
        return parent::_beforeSave();
    }  
    
    public function __clone()
    {
        $this->setId(null);
        $this->_item    = null;
        return $this;
    }    
    
}