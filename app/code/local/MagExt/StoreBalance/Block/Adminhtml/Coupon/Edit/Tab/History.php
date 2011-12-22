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
 * Coupon history tab
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Coupon_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setDefaultSort('modified_date');
        $this->setDefaultDir('desc');
        $this->setId('historyGrid');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mgxstorebalance/coupon_history')
            ->getCollection()
            ->addCouponFilter(Mage::registry('current_storebalance_coupon')->getId());
        $this->setCollection($collection);
            
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('modified_date', array(
            'header'    => $this->_helper()->__('Modified On'),
            'index'     => 'modified_date',
            'type'      => 'datetime',
            'width'     => 150,
        ));
        $this->addColumn('action', array(
            'header'    => $this->_helper()->__('Action'),
            'index'     => 'action',
            'type'      => 'options',
            'width'     => 130,
            'sortable'  => false,
            'options'   => Mage::getSingleton('mgxstorebalance/coupon_history')->getActionOptions(),
        ));
        $this->addColumn('balance', array(
            'header'    => $this->_helper()->__('Balance'),
            'index'     => 'balance',
            'type'      => 'price',
            'width'     => 100,
            'sortable'  => false,
            'filter'    => false,
            'currency_code' => Mage::app()->getWebsite(Mage::registry('current_storebalance_coupon')->getWebsiteId())->getBaseCurrencyCode(),
        ));
        $this->addColumn('comment', array(
            'header'    => $this->_helper()->__('Comment'),
            'index'     => 'comment',
            'sortable'  => false,
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Return helper
     * @return MagExt_StoreBalance_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mgxstorebalance');
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyGrid', array('_current'=> true));
    }
}