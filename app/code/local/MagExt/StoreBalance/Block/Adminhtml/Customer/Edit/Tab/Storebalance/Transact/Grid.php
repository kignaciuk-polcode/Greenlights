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
 * Transactions Grid
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Customer_Edit_Tab_Storebalance_Transact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('storebalanceTransactGrid');
        $this->setDefaultSort('modified_date');
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('value', array(
            'header'    => Mage::helper('mgxstorebalance')->__('Store Balance'),
            'index'     => 'value',
            'type'      => 'currency',
            'sortable'  => false,
            'filter'    => false,
            'width'     => '50px',
            'renderer'  => 'mgxstorebalance/adminhtml_widget_grid_column_renderer_currency'
        ));
        $this->addColumn('value_change', array(
            'header'    => Mage::helper('mgxstorebalance')->__('Balance Change'),
            'index'     => 'value_change',
            'sortable'  => false,
            'filter'    => false,
            'width'     => '50px',
            'renderer'  => 'mgxstorebalance/adminhtml_widget_grid_column_renderer_currency'
        ));
        $this->addColumn('website_id', array(
            'header'    => Mage::helper('mgxstorebalance')->__('Website'),
            'index'     => 'website_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
            'sortable'  => false,
            'width'     => '120px',
        ));
        $this->addColumn('modified_date', array(
            'header'   => Mage::helper('mgxstorebalance')->__('Modified On'),
            'index'    => 'modified_date',
            'type'     => 'datetime',
            'width'    => '150px',
            'filter'   => false,
        ));
        $this->addColumn('action', array(
            'header'    => Mage::helper('mgxstorebalance')->__('Action'),
            'width'     => '50px',
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => Mage::getSingleton('mgxstorebalance/balance_transact')->getActionOptions(),
        ));
        $this->addColumn('comment', array(
            'header'    => Mage::helper('mgxstorebalance')->__('Comment'),
            'index'     => 'comment',
            'type'      => 'text',
            'nl2br'     => true,
            'sortable'  => false,
            'filter'   => false,
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mgxstorebalance/balance_transact')
            ->getCollection()
            ->addCustomerFilter(Mage::registry('current_customer')->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/transactGrid', array('_current'=> true));
    }
}