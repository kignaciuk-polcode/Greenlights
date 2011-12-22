<?php
/**
 * MagExtension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagExtension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magextension.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magextension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magextension.com for more information.
 *
 * @category   MagExt
 * @package    MagExt_StoreBalance
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */
 
/**
 * The source model of products for purchasing Store Balance Units
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_System_Config_Source_Product_List
{
    protected $_options = array();
    
    public function toOptionArray()
    {
        $store = Mage::app()->getStore();
        
        if (!$this->_options) {
            $productCollection = Mage::getModel('catalog/product')->getCollection();
            /* @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
            $productCollection->addAttributeToFilter('store_balance_refill', 1)
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->load();
            foreach ($productCollection as $product)
            {
                $this->_options[] = array(
                    'value'=>$product->getId(), 
                    'label'=>$product->getName().' ('.$store->getBaseCurrency()->format($product->getPrice(), array(), false).')');
            }
        }

        $options = $this->_options;
        array_unshift($options, array('value'=>'', 'label'=> Mage::helper('mgxstorebalance')->__('--Please Select--')));
        
        return $options;
    }
}