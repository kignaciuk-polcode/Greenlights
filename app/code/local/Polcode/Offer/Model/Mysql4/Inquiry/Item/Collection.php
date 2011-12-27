<?php

class Polcode_Offer_Model_Mysql4_Inquiry_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected $_productInStock = false;    

    protected $_itemsQty;
    
    public function _construct() {
        //parent::__construct();
        $this->_init('offer/inquiry_item');
    }
    
    public function setInStockFilter($flag = true)
    {
        $this->_productInStock = (bool)$flag;
        return $this;
    }
    
    public function getItemsQty(){
        if (is_null($this->_itemsQty)) {
            $this->_itemsQty = 0;
            foreach ($this as $inquiryItem) {
                $qty = $inquiryItem->getProductQty();
                $this->_itemsQty += ($qty === 0) ? 1 : $qty;
            }
        }

        return (int)$this->_itemsQty;
    }    
    
}