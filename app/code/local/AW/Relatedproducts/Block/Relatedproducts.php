<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Who_bought_this_also_bought
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
/**
 * Related products Block
 */
class AW_Relatedproducts_Block_Relatedproducts extends Mage_Catalog_Block_Product_Abstract {
    /**
     * Default count of products to show
     */
    const ONE_TIME_INSTALL_ORDERS_LIMIT = 3;

    /**
     * Config path to Products to display
     */
    const XML_PATH_PRODUCTS_TO_DISPLAY = 'relatedproducts/general/products_to_display';

    /**
     * Config path to Same category
     */
    const XML_PATH_SAME_CATEGORY = 'relatedproducts/general/same_category';

    /**
     * Config path to Enabled
     */
    const XML_PATH_ENABLED = 'relatedproducts/general/enabled';

    /**
     * Class to remove
     */
    const COMMUNITY_RELATED_CLASS = 'Mage_Catalog_Block_Product_List_Related';


    const TEMPLATE_EMPTY = 'catalog/product/list/empty.phtml';

    protected $_itemCollection;
    protected $_relatedCollection;
    protected $_target;

    /**
     * Retrives current product id
     * @return integer|null
     */
    public function getProductId() {
        return Mage::registry('product') ? Mage::registry('product')->getId() : null;
    }

    /**
     * Retrives current category id
     * @return integer|null
     */
    public function getCategoryId() {
        return Mage::registry('category') ? Mage::registry('category')->getId() : null;
    }

    /**
     * Retrives block is enabled from config
     * @return boolean
     */
    public function getEnabled() {
        return!!Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function getProduct() {
        return Mage::registry('product');
    }

    public function disableRelated() {
        $deleteId = null;
        $i = 0;
        foreach ($this->getParentBlock()->_children as $child) {
            if (get_class($child) == self::COMMUNITY_RELATED_CLASS) {
                $deleteId = $i;
                $child->setTemplate(self::TEMPLATE_EMPTY);
            }
            $i++;
        }
    }

    public function setTarget($value) {
        $this->_target = $value;

        if ($this->getTarget() == 'community') {
            if ($this->getEnabled() && !Mage::helper('relatedproducts')->getExtDisabled()) {
                $this->disableRelated();
            }
        }

        return $this;
    }

    public function getTarget() {
        return $this->_target;
    }

    protected function _beforeToHtml() {
        $this->_prepareProductPrices();
        parent::_beforeToHtml();
    }

    /**
     * Rtrives number of products to display
     * @return integer
     */
    public function getProductsToDisplay() {
        if (($num = Mage::getStoreConfig(self::XML_PATH_PRODUCTS_TO_DISPLAY)) > 0) {
            return $num;
        } else {
            return self::ONE_TIME_INSTALL_ORDERS_LIMIT;
        }
    }

    /**
     * Retrives table name for Model Entity Name
     * @param string $modelEntity
     * @return string
     */
    public function getTableName($modelEntity) {
        try {
            $table = Mage::getSingleton('core/resource')->getTableName($modelEntity);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
        return $table;
    }

    /**
     * Index sales data for current product
     * @param int|string $productId
     * @return AW_Relatedproducts_Block_Relatedproducts
     */
    protected function _installForProduct($productId) {

        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->addAttributeToSelect('*')->addAttributeToFilter('status', array('in' => Mage::helper('relatedproducts')->getAllowStatuses()));
        $storeId = Mage::app()->getStore()->getId();

        $catalogCategoryTable = $this->getTableName('catalog/category_product');
        if (Mage::helper('relatedproducts')->isEnterprise()) {
            $itemTable = $this->getTableName('sales/order_item');
            $orderAlias = 'main_table';
        } elseif (Mage::helper('relatedproducts')->checkVersion('1.4.1.0')) {
            $itemTable = $this->getTableName('sales/order_item');
            $orderAlias = 'main_table';
        } else {
            $itemTable = $orders->getTable('sales_flat_order_item');
            $orderAlias = 'e';
        }

        $orders->getSelect()->join(array('item' => $itemTable), $orderAlias . ".entity_id = item.order_id AND item.parent_item_id IS NULL", array())
                ->join(array('item1' => $itemTable), $orderAlias . ".entity_id = item1.order_id AND item1.parent_item_id IS NULL", array('i_count' => 'COUNT( item1.product_id )'))
                ->where($orderAlias . '.store_id = ?', $storeId)
                ->where('item.product_id = ?', $productId)
                ->group($orderAlias . '.entity_id')
                ->order('i_count', 'DESC')
                ->limit($this->getProductsToDisplay());

        if (Mage::getStoreConfig(self::XML_PATH_SAME_CATEGORY)) {
            $orders->getSelect()
                    # Join cats of main product
                    ->joinRight(array('mainProd' => $catalogCategoryTable), "mainProd.product_id = item.product_id", array())
                    # Join cats of sub products
                    ->joinLeft(array('subProd' => $catalogCategoryTable), "subProd.product_id = item1.product_id", array())
                    ->where('mainProd.category_id = subProd.category_id')
            ;
        }

        $orders->load();

        $ids = array();

        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->load($order->getId());
            $items = $order->getAllItems();
            if (count($items)) {
                $ids = array();
                foreach ($items as $itemId => $item) {
                    if (!$item->getParentItemId()) {
                        array_push($ids, $item->getProductId());
                    }
                }
            }
            if (Mage::getStoreConfig(self::XML_PATH_SAME_CATEGORY)) {
                $ids = Mage::helper('relatedproducts')->checkForCrossCategory($ids, $this->getProductId());
            }
            if (count($ids) > 1) {
                Mage::helper('relatedproducts')->updateRelations($ids);
            }
        }
        return $this;
    }

    public function getCollection() {
        if (!$this->_relatedCollection) {
            if ($productId = $this->getProductId()) {
                return $this->_relatedCollection = Mage::getModel('relatedproducts/relatedproducts')
                        ->getCollection()
                        ->addProductFilter($productId)
                        ->addStoreFilter()
                        ->load();
            } else {
                return null;
            }
        } else {
            return $this->_relatedCollection;
        }
    }

    public function getUpdatedCollection() {
        $this->_relatedCollection = null;
        return $this->getCollection();
    }

    public function getRelatedProductsCollection() {
        $items = array();
        if (count($this->getCollection())) {
            $items = $this->getCollection()->getItems();
        } elseif (count($this->_installForProduct($this->getProductId())->getUpdatedCollection())) {

            $items = $this->getCollection();
        }

        $related_ids = array();

        foreach ($items as $item) {


            # actually runs only once max, for 1 collection element
            $related_items = unserialize($item->getData('related_array'));

            arsort($related_items, SORT_NUMERIC); //order by number of purchases

            $related_items = array_slice($related_items, 0, $this->getProductsToDisplay(), true);

            foreach ($related_items as $key => $value) {
                array_push($related_ids, $key);
            }
        }


        $this->_itemCollection = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->getCollection();


        Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId());

        $this->_itemCollection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);
        $this->_itemCollection->addAttributeToFilter('entity_id', array('in' => $related_ids));
        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this->_returnSortedArray($this->_itemCollection, $related_ids);
    }

    /**
     * Sort items by relevance
     * @param Mage_Catalog_Model_Mysql4_Product_Collection $collection
     * @param array $keysToSort
     * @return array 
     */
    protected function _returnSortedArray($collection, $keysToSort = null) {
        $array = array();
        if ($keysToSort && is_array($keysToSort)) {
            foreach ($keysToSort as $keyId) {
                if ($product = $this->_getItemFromCollection($collection, $keyId)) {
                    $array[] = $product;
                }
            }
        }
        return $array;
    }

    protected function _getItemFromCollection($collection, $id) {
        foreach ($collection as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }
    }

    private function _prepareProductPrices() {

        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        $this->addPriceBlockType('giftcard', 'enterprise_giftcard/catalog_product_price', 'giftcard/catalog/product/price.phtml');
    }

}