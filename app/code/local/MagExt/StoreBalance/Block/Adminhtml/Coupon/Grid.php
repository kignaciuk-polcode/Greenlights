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
 * Coupon grid
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Coupon_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('couponGrid');
		$this->setSaveParametersInSession(true);
		$this->setDefaultSort('coupon_id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
        $this->setVarNameFilter('storebalance_coupon_filter');
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('coupon_id',
            array(
                'header'=> $this->_helper()->__('ID'),
                'width' => '50px',
                'index' => 'coupon_id',
        ));
        $this->addColumn('hash',
            array(
                'header'=> $this->_helper()->__('Coupon'),
                'width' => '250px',
                'index' => 'hash',
        ));
        $this->addColumn('balance',
            array(
                'header'=> $this->_helper()->__('Balance'),
                'width' => '80px',
                'type'  => 'number',
            	//'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                'index' => 'balance',
                'renderer' => 'mgxstorebalance_admin/widget_grid_column_renderer_currency'
        ));
        $this->addColumn('website_id',
            array(
                'header'=> $this->_helper()->__('Website'),
                'type'  => 'options',
                'index' => 'website_id',
            	'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
        ));
        $this->addColumn('from_date', 
            array(
                'header'    => $this->_helper()->__('Date Start'),
                'align'     => 'left',
                'width'     => '50px',
                'type'      => 'date',
                'index'     => 'from_date',
        ));

        $this->addColumn('to_date',
            array(
                'header'    => $this->_helper()->__('Date Expire'),
                'align'     => 'left',
                'width'     => '50px',
                'type'      => 'date',
                'default'   => '--',
                'index'     => 'to_date',
        ));
        $this->addColumn('used_date',
            array(
                'header'=> $this->_helper()->__('Last Used'),
                'width' => '50px',
                'type'  => 'date',
                'index' => 'used_date',
            	'default' => '-',
        ));
        $this->addColumn('is_active',
            array(
                'header'=> $this->_helper()->__('Status'),
                'width' => 30,
                'type'  => 'options',
                'index' => 'is_active',
            	'options' => Mage::getSingleton('mgxstorebalance/coupon')->getStatusOptionArray(),
        ));
        
        return parent::_prepareColumns();
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('mgxstorebalance/coupon_collection');
		$this->setCollection($collection);
        return parent::_prepareCollection();
	}
	
    protected function _prepareMassaction() 
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('coupon');
        
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('mgxstorebalance')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('mgxstorebalance')->__('Are you sure?')
        ));
        
        $statuses = Mage::getSingleton('mgxstorebalance/coupon')->getStatusOptionArray(2);
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('mgxstorebalance')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'activity' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('mgxstorebalance')->__('Status'),
                         'values' => $statuses,
                         'value' => 2,
                     )
             )
        ));
    }
	
	public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'    => $row->getId()
        ));
    }
    
    protected function _helper()
    {
    	return Mage::helper('mgxstorebalance');
    }
}