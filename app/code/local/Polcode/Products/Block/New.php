<?php

class Polcode_Products_Block_New extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        
        $storeId = Mage::app()->getStore()->getId();
        
        $products = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->setOrder('created_at', 'desc');
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        
        $products->getSelect()->limit(8);
        
        $this->setProductCollection($products);
    }
    
}
