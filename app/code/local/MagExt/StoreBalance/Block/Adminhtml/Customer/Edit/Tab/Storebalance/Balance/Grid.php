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
 * Balance Grid
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Customer_Edit_Tab_Storebalance_Balance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setId('balanceGrid');
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('website_id', array(
            'header'   => Mage::helper('mgxstorebalance')->__('Website'),
            'index'    => 'website_id',
            'type'     => 'options',
            'options'  => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
            'width'    => 250,
            'sortable' => false,
        ));
        
        $this->addColumn('value', array(
            'header'   => Mage::helper('mgxstorebalance')->__('Balance'),
            'index'    => 'value',
            'sortable' => false,
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareCollection()
    {
        $customer = Mage::registry('current_customer');
        $collection = Mage::getResourceModel('mgxstorebalance/balance_collection')
            ->addCustomerFilter($customer->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}