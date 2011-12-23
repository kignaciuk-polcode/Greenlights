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
 * Relatedproducts Events observer
 */
class AW_Relatedproducts_Model_Observer
{
    /**
     * Observe placing of new order
     * @param Varien_Object $observer
     */
	public function updateRelatedproductsOrderStatus($observer)
    {
		$order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();

        $oldStatus = $order->getOrigData('status');
        $newStatus = $order->getData('status');
        if (($oldStatus && $oldStatus != $newStatus)){
            if (!in_array($newStatus, Mage::helper('relatedproducts')->getAllowStatuses($storeId)) &&
                    in_array($oldStatus, Mage::helper('relatedproducts')->getAllowStatuses($storeId))){
                Mage::getModel('relatedproducts/relatedproducts')->getResource()->resetStatistics();
            }

        }
        if (!in_array($order->getStatus(), Mage::helper('relatedproducts')->getAllowStatuses($storeId))){
            return;
        }

    	$ids = array();
        $items = $order->getAllItems();
        if(count($items)){
                $ids = array();
                foreach ($items as $itemId => $item){
                    if (!$item->getParentItemId()){
                        array_push($ids, $item->getProductId());
                    }
                }
        }
        if (Mage::getStoreConfig(AW_Relatedproducts_Block_Relatedproducts::XML_PATH_SAME_CATEGORY)){
            $ids = Mage::helper('relatedproducts')->checkForCrossCategory($ids);
        }
        if (count($ids)>1){
            Mage::helper('relatedproducts')->updateRelations($ids);
        }
	}
}