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
class AW_Relatedproducts_Model_Mysql4_Relatedproducts extends Mage_Core_Model_Mysql4_Abstract
{

	public function _construct()
    {    
        $this->_init('relatedproducts/relatedproducts', 'entity_id');
    }
    
/*    public function updateRelation($productId, $relationsArr){
    	$prop = array(
            'related_array' => $relationsArr
		);
    	$this->_getWriteAdapter()->update(Mage::getSingleton('core/resource')->getTableName('aw_relatedproducts'), $prop, $this->_getWriteAdapter()
    		->quoteInto('product_id=?', $productId));
    }
        
    public function insertRelation($productId, $relationsArr){
    	
    } 
    */

    public function resetStatistics()
    {
        $this->_getWriteAdapter()->delete($this->getMainTable());
    }

}