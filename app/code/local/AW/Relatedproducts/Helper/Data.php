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
 */class AW_Relatedproducts_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Checking products for Same Category
     *
     * @param array $ids Array with product ids
     * @return array
     */
    public function checkForCrossCategory($ids, $checkedProduct = null) {
        $new = array();
        foreach ($ids as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $catIds = $product->getCategoryIds();
            foreach ($catIds as $catId) {
                foreach ($ids as $subProductId) {
                    if ($subProductId != $productId) {
                        $subProduct = Mage::getModel('catalog/product')->load($subProductId);

                        $crossCat = false;
                        foreach ($subProduct->getCategoryIds() as $subCatId) {
                            if ($subCatId === $catId) {
                                $crossCat = true;
                            }
                        }

                        if ($checkedProduct) {
                            if ($crossCat && ($productId == $checkedProduct)) {
                                # Add pair
                                $new[] = $productId;
                                $new[] = $subProductId;
                            }
                        } else {
                            if ($crossCat) {
                                # Add pair
                                $new[] = $productId;
                                $new[] = $subProductId;
                            }
                        }
                    }
                }
            }
        }
        return array_unique($new);
    }

    /*
     * 	Take $relatedIds array and establish relations to each other
     */

    function updateRelations($relatedIds) {
        $model = Mage::getResourceModel('relatedproducts/relatedproducts');

        $arr = array();
        foreach ($relatedIds as $id) {
            //fetch relations for each of the ID's
            $model = Mage::getModel('relatedproducts/relatedproducts');

            $coll = $model->getCollection()
                            ->addStoreFilter()
                            ->addProductFilter($id)
                            ->load();
            if (sizeof($coll) == 0) {
                foreach ($relatedIds as $i) {
                    if ($i != $id) //not the product for itself
                        $arr[$i] = 1; //set relation rate to 1 for all


                }
                $arr = serialize($arr);
                $model
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setProductId($id)
                        ->setRelatedArray($arr)
                        ->save();
            }
            else {
                foreach ($coll as $c) {
                    $incrementalId = $c->getId();
                    //take current related products
                    $arr = unserialize($c->getData('related_array'));
                    foreach ($relatedIds as $i) {
                        if ($i != $id) { //not the product for itself
                            if (!empty($arr[$i]))
                                $arr[$i] += 1; //increment the relation counter
                        else
                                $arr[$i] = 1;
                        }
                    }
                }
                $arr = serialize($arr);
                $model
                        ->setId($incrementalId)
                        ->setProductId($id)
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setRelatedArray($arr)
                        ->save();
            }
            $arr = array();
        }
    }

    public function isEnterprise() {

        if (Mage::getConfig()->getNode('modules/Enterprise_Enterprise')) {
            return true;
        }
        return false;
    }

    public function checkVersion($version) {
        return version_compare(Mage::getVersion(), $version, '>=');
    }

    /**
     * Retrives Advanced Reviews Disabled Flag
     * @return boolean
     */
    public function getExtDisabled() {
        return Mage::getStoreConfig('advanced/modules_disable_output/AW_Relatedproducts');
    }

    /**
     *
     * @param <type> $storeId
     * @return array
     */
    public function getAllowStatuses($storeId = null) {
        $res = explode(",", Mage::getStoreConfig('relatedproducts/general/process_orders', $storeId));
        return count($res) ? $res : array(Mage_Sales_Model_Order::STATE_COMPLETE);
    }

}

