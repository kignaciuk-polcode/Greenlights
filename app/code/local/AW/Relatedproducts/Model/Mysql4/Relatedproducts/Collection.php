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
class AW_Relatedproducts_Model_Mysql4_Relatedproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
		$this->_table = Mage::getSingleton('core/resource')->getTableName('relatedproducts/relatedproducts');
		$this->_init('relatedproducts/relatedproducts');
    }
    
    public function addProductFilter($productId)
    {
        $this->getSelect()
             ->where('main_table.product_id=?', $productId);
        return $this;
    }
    
    public function addStoreFilter($storeId = null)
    {
        if ($storeId === null){
            $storeId = Mage::app()->getStore()->getId();
        }
        $this->getSelect()
             ->where('main_table.store_id=?', $storeId);
        return $this;        
    }

}