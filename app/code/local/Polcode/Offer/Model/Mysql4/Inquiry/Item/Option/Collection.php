<?php

class Polcode_Offer_Model_Mysql4_Inquiry_Item_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {


    protected $_optionsByItem    = array();

    protected $_optionsByProduct = array();

    protected function _construct()
    {
        $this->_init('offer/inquiry_item_option');
    }


    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this as $option) {
            $optionId   = $option->getId();
            $itemId     = $option->getInquiryItemId();
            $productId  = $option->getProductId();
            if (isset($this->_optionsByItem[$itemId])) {
                $this->_optionsByItem[$itemId][] = $optionId;
            } else {
                $this->_optionsByItem[$itemId] = array($optionId);
            }
            if (isset($this->_optionsByProduct[$productId])) {
                $this->_optionsByProduct[$productId][] = $optionId;
            } else {
                $this->_optionsByProduct[$productId] = array($optionId);
            }
        }

        return $this;
    }

    public function addItemFilter($item)
    {
        if (empty($item)) {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        } else if (is_array($item)) {
            $this->addFieldToFilter('inquiry_item_id', array('in' => $item));
        } else if ($item instanceof Polcode_Offer_Model_Inquiry_Item) {
            $this->addFieldToFilter('inquiry_item_id', $item->getId());
        } else {
            $this->addFieldToFilter('inquiry_item_id', $item);
        }

        return $this;
    }

    public function getProductIds()
    {
        $this->load();

        return array_keys($this->_optionsByProduct);
    }


    public function getOptionsByItem($item)
    {
        if ($item instanceof Polcode_Offer_Model_Inquiry_Item) {
            $itemId = $item->getId();
        } else {
            $itemId = $item;
        }

        $this->load();

        $options = array();
        if (isset($this->_optionsByItem[$itemId])) {
            foreach ($this->_optionsByItem[$itemId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }

    public function getOptionsByProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $this->load();

        $options = array();
        if (isset($this->_optionsByProduct[$productId])) {
            foreach ($this->_optionsByProduct[$productId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }    
    
} 
