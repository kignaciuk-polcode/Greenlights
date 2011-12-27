<?php

class Polcode_Offer_Model_Inquiry extends Mage_Core_Model_Abstract {

    protected $_itemCollection = null;
    protected $_store = null;
    protected $_storeIds = null;

    public function _construct() {
        parent::_construct();
        $this->_init('offer/inquiry');
    }

    protected function _getCustomer() {
              
        return Mage::getModel('customer/customer')->load($this->getCustomerId());
    }

    public function addItem(Polcode_Offer_Model_Inquiry_Item $item) {
        $item->setInquiry($this);
        if (!$item->getId()) {
            $this->getItemCollection()->addItem($item);
            Mage::dispatchEvent('inquiry_add_item', array('item' => $item));
        }
        return $this;
    }

    public function addNewItem($product, $buyRequest = null, $forciblySetQty = false) {
        /*
         * Always load product, to ensure:
         * a) we have new instance and do not interfere with other products in inquiry
         * b) product has full set of attributes
         */

        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
            // Maybe force some store by wishlist internal properties
            $storeId = $product->hasInquiryStoreId() ? $product->getInquiryStoreId() : $product->getStoreId();
        } else {
            $productId = (int) $product;
            if ($buyRequest->getStoreId()) {
                $storeId = $buyRequest->getStoreId();
            } else {
                $storeId = Mage::app()->getStore()->getId();
            }
        }

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($productId);

        if ($buyRequest instanceof Varien_Object) {
            $_buyRequest = $buyRequest;
        } elseif (is_string($buyRequest)) {
            $_buyRequest = new Varien_Object(unserialize($buyRequest));
        } elseif (is_array($buyRequest)) {
            $_buyRequest = new Varien_Object($buyRequest);
        } else {
            $_buyRequest = new Varien_Object();
        }


        $cartCandidates = $product->getTypeInstance(true)
                ->processConfiguration($_buyRequest, $product);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        $errors = array();
        $items = array();



        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }
            $candidate->setInquiryStoreId($storeId);

            $qty = $candidate->getQty() ? $candidate->getQty() : 1; // No null values as qty. Convert zero to 1.
            $item = $this->_addCatalogProduct($candidate, $qty, $forciblySetQty);
            $items[] = $item;

            // Collect errors instead of throwing first one
            if ($item->getHasError()) {
                $errors[] = $item->getMessage();
            }
        }

        Mage::dispatchEvent('inquiry_product_add_after', array('items' => $items));

        return $item;
    }

    public function loadByCustomer($customer, $create = false) {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $customer = (int) $customer;
        $customerIdFieldName = $this->_getResource()->getCustomerIdFieldName();

        $this->_getResource()->loadInquiry($customerIdFieldName, $customer, $this);

        if (!$this->getId() && $create) {
            $this->setCustomerId($customer);
            $this->setUpdatedAt(now());
            $this->setSharingCode($this->_getSharingRandomCode());
            $this->save();
        }
        return $this;
    }

    protected function _getSharingRandomCode() {
        return Mage::helper('core')->uniqHash();
    }

    protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty = 1, $forciblySetQty = false) {

        $item = null;
        foreach ($this->getItemCollection() as $_item) {
            if ($_item->representProduct($product)) {
                $item = $_item;
                break;
            }
        }


        if ($item === null) {
            $storeId = $product->hasInquiryStoreId() ? $product->getInquiryStoreId() : $this->getStore()->getId();


            $item = Mage::getModel('offer/inquiry_item');
            try {

                $item->setProductId($product->getId())
                        ->setInquiryId($this->getId())
                        ->setAddedAt(now())
                        ->setStoreId($storeId)
                        ->setOptions($product->getCustomOptions())
                        ->setProduct($product)
                        ->setProductQty($qty)
                        ->save();
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
        } else {
            $qty = $forciblySetQty ? $qty : $item->getProductQty() + $qty;
            $item->setProductQty($qty)
                    ->save();
        }

        $this->addItem($item);

        return $item;
    }

    public function getItemCollection() {
        if (is_null($this->_itemCollection)) {

            /** @var $currentWebsiteOnly boolean */
            $currentWebsiteOnly = !Mage::app()->getStore()->isAdmin();
            $this->_itemCollection = Mage::getResourceModel('offer/inquiry_item_collection')
                    ->addFieldToFilter('inquiry_id', $this->getId());
            //->addFieldToFilter('store_id',array('in' => $this->getSharedStoreIds($currentWebsiteOnly)));
        }
//        var_dump($this->);
//        die();
        return $this->_itemCollection;
    }

    public function getStore() {
        if (is_null($this->_store)) {
            $this->setStore(Mage::app()->getStore());
        }
        return $this->_store;
    }

    public function setStore($store) {
        $this->_store = $store;
        return $this;
    }

    public function getSharedStoreIds($current = true) {
        if (is_null($this->_storeIds) || !is_array($this->_storeIds)) {
            if ($current) {
                $this->_storeIds = $this->getStore()->getWebsite()->getStoreIds();
            } else {
                $_storeIds = array();
                $stores = Mage::app()->getStores();
                foreach ($stores as $store) {
                    $_storeIds[] = $store->getId();
                }
                $this->_storeIds = $_storeIds;
            }
        }
        return $this->_storeIds;
    }

    public function getItem($itemId) {
        if (!$itemId) {
            return false;
        }
        return $this->getItemCollection()->getItemById($itemId);
    }

    public function updateItem($itemId, $buyRequest, $params = null) {
        $item = $this->getItem((int) $itemId);
        if (!$item) {
            Mage::throwException(Mage::helper('offer/inquiry')->__('Cannot specify inquiry item.'));
        }

        $product = $item->getProduct();
        $productId = $product->getId();
        if ($productId) {
            if (!$params) {
                $params = new Varien_Object();
            } else if (is_array($params)) {
                $params = new Varien_Object($params);
            }
            $params->setCurrentConfig($item->getBuyRequest());
            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest($buyRequest, $params);

            $product->setInquiryStoreId($item->getStoreId());
            $items = $this->getItemCollection();
            $isForceSetQuantity = true;
            foreach ($items as $_item) {
                /* @var $_item Mage_Wishlist_Model_Item */
                if ($_item->getProductId() == $product->getId()
                        && $_item->representProduct($product)
                        && $_item->getId() != $item->getId()) {
                    // We do not add new inquiry item, but updating the existing one
                    $isForceSetQuantity = false;
                }
            }
            $resultItem = $this->addNewItem($product, $buyRequest, $isForceSetQuantity);
            /**
             * Error message
             */
            if (is_string($resultItem)) {
                Mage::throwException(Mage::helper('checkout')->__($resultItem));
            }

            if ($resultItem->getId() != $itemId) {
//                if ($resultItem->getDescription() != $item->getDescription()) {
//                    $resultItem->setDescription($item->getDescription())->save();
//                }
                $item->isDeleted(true);
                $this->setDataChanges(true);
            } else {
                $resultItem->setProductQty($buyRequest->getQty() * 1);
                $resultItem->setOrigData('product_qty', 0);
            }
        } else {
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }
        return $this;
    }

    public function getItemsCount() {

        return $this->getItemCollection()->getSize();
    }
    
    public function isSubmitted(){
        
        if($this->getSubmitted()){
            return true;
        }
        return false;
        
    }
    
    public function getCustomerName(){
        
        if($this->_getCustomer()->getFirstname()){
        
            $customerName = $this->_getCustomer()->getFirstname() . ' ' . $this->_getCustomer()->getLastname();
        }
        else {
            $customerName = Mage::helper('offer')->__('Guest');
        }
        return $customerName;
    }
    
    public function getCustomerEmail(){
        
        if($this->_getCustomer()->getEmail()){
        
            $customerEmail = $this->_getCustomer()->getEmail();
        }
        else {
            $customerEmail = Mage::helper('offer')->__('Guest');
        }
        return $customerEmail;
    }    
    
    
    public function getSubtotal(){
        
      $subtotal = null;
        
      foreach($this->getItemCollection() as $_item){
          
          $productPrice = Mage::getModel('catalog/product')->load($_item->getProductId())->getPrice();
          $subtotal += $_item->getProductQty() * $productPrice;

      }      
      return $subtotal;
        
        
        
    }
        

}