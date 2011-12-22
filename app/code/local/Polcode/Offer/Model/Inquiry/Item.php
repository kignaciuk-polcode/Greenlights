<?php

class Polcode_Offer_Model_Inquiry_Item extends Mage_Core_Model_Abstract {

    protected $_options = array();
    protected $_optionsByCode = array();
    protected $_flagOptionsSaved = null;
    protected $_notRepresentOptions = array('info_buyRequest');    

    protected function _construct() {
        parent::_construct();
        $this->_init('offer/inquiry_item');
    }

    protected function _addOptionCode($option) {
        if (!isset($this->_optionsByCode[$option->getCode()])) {
            $this->_optionsByCode[$option->getCode()] = $option;
        } else {
            Mage::throwException(Mage::helper('sales')->__('An item option with code %s already exists.', $option->getCode()));
        }
        return $this;
    }

    public function getProduct() {
        $product = $this->_getData('product');
        if (is_null($product)) {
            if (!$this->getProductId()) {
                Mage::throwException(Mage::helper('offer/inquiry')->__('Cannot specify product.'));
            }

            $product = Mage::getModel('catalog/product')
                    ->setStoreId($this->getStoreId())
                    ->load($this->getProductId());

            $this->setData('product', $product);
        }

        /**
         * Reset product final price because it related to custom options
         */
        $product->setFinalPrice(null);
        $product->setCustomOptions($this->_optionsByCode);
        return $product;
    }

    public function canHaveQty() {
        $product = $this->getProduct();
        return $product->getTypeId() != Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE;
    }

    public function getOptionByCode($code) {
        if (isset($this->_optionsByCode[$code]) && !$this->_optionsByCode[$code]->isDeleted()) {
            return $this->_optionsByCode[$code];
        }
        return null;
    }

    public function getAddToCartQty(Mage_Offer_Model_Inqury_Item $item) {
        $qty = $this->getProductQty($item);
        return $qty ? $qty : 1;
    }

    public function addOption($option) {
        if (is_array($option)) {
            $option = Mage::getModel('offer/inquiry_item_option')->setData($option)
                    ->setItem($this);
        } else if ($option instanceof Polcode_Offer_Model_Inquiry_Item_Option) {
            $option->setItem($this);
        } else if ($option instanceof Varien_Object) {                       
            $option = Mage::getModel('offer/inquiry_item_option')->setData($option->getData())
                    ->setProduct($option->getProduct())
                    ->setItem($this);            
        } else {
            Mage::throwException(Mage::helper('offer/inquiry')->__('Invalid item option format.'));
        }

        $exOption = $this->getOptionByCode($option->getCode());

        if ($exOption) {
            $exOption->addData($option->getData());
        } else {
            $this->_addOptionCode($option);
            $this->_options[] = $option;
        }


        return $this;
    }

    public function setOptions($options) {

        foreach ($options as $option) {
            $this->addOption($option);
        }

        return $this;
    }

    protected function _saveItemOptions() {
        foreach ($this->_options as $index => $option) {
            if ($option->isDeleted()) {
                $option->delete();
                unset($this->_options[$index]);
                unset($this->_optionsByCode[$option->getCode()]);
            } else {
                $option->save();
            }
        }

        $this->_flagOptionsSaved = true; // Report to watchers that options were saved

        return $this;
    }

    public function save() {
        $hasDataChanges = $this->hasDataChanges();
        $this->_flagOptionsSaved = false;

        parent::save();

        if ($hasDataChanges && !$this->_flagOptionsSaved) {
            $this->_saveItemOptions();
        }
    }

    protected function _afterSave() {
        $this->_saveItemOptions();
        return parent::_afterSave();
    }

    public function validate() {
        if (!$this->getInquiryId()) {
            Mage::throwException(Mage::helper('offer/inquiry')->__('Cannot specify offer inquiry.'));
        }
        if (!$this->getProductId()) {
            Mage::throwException(Mage::helper('offer/inquiry')->__('Cannot specify product.'));
        }

        return true;
    }

    protected function _beforeSave() {
        parent::_beforeSave();

        // validate required item data
        $this->validate();

        // set current store id if it is not defined
        if (is_null($this->getStoreId())) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        // set current date if added at data is not defined
        if (is_null($this->getCreatedAt())) {
            $this->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        return $this;
    }
    
    public function representProduct($product)
    {
        $itemProduct = $this->getProduct();
        if ($itemProduct->getId() != $product->getId()) {
            return false;
        }

        $itemOptions    = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if(!$this->compareOptions($itemOptions, $productOptions)){
            return false;
        }
        if(!$this->compareOptions($productOptions, $itemOptions)){
            return false;
        }
        return true;
    }  
    
    public function compareOptions($options1, $options2)
    {
        foreach ($options1 as $option) {
            $code = $option->getCode();
            if (in_array($code, $this->_notRepresentOptions )) {
                continue;
            }
            if ( !isset($options2[$code])
                || ($options2[$code]->getValue() === null)
                || $options2[$code]->getValue() != $option->getValue()) {
                return false;
            }
        }
        return true;
    }
    
    
    public function getBuyRequest()
    {
        $option = $this->getOptionByCode('info_buyRequest');
        
        
        
        $initialData = $option ? unserialize($option->getValue()) : null;

        // There can be wrong data due to bug in Grouped products - it formed 'info_buyRequest' as Varien_Object
        if ($initialData instanceof Varien_Object) {
            $initialData = $initialData->getData();
        }

        $buyRequest = new Varien_Object($initialData);
                      
        $buyRequest->setOriginalQty($buyRequest->getQty())
            ->setQty($this->getProductQty() * 1);

        return $buyRequest;
    }    
    

}