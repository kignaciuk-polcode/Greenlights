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
 * Coupon tabs widget
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Coupon_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('storebalance_coupon_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mgxstorebalance')->__('Store Balance Coupon'));
    }
    
    protected function _beforeToHtml()
    {
        $couponModel = Mage::registry('current_storebalance_coupon');
        $this->addTab('details_section', array(
            'label'     => Mage::helper('mgxstorebalance')->__('Details'),
            'title'     => Mage::helper('mgxstorebalance')->__('Details'),
            'content'   => $this->getLayout()->createBlock('mgxstorebalance_admin/coupon_edit_tab_details')->toHtml(),
            'active'    => true
        ));
        if ($couponModel->getIsNew())
        {
            $this->addTab('settings_section', array(
                'label'     => Mage::helper('mgxstorebalance')->__('Settings'),
                'title'     => Mage::helper('mgxstorebalance')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('mgxstorebalance_admin/coupon_edit_tab_settings')->toHtml(),
            ));
        }

        if (!$couponModel->getIsNew())
        {
            $this->addTab('history_section', array(
                'label'     => Mage::helper('mgxstorebalance')->__('Coupon History'),
                'title'     => Mage::helper('mgxstorebalance')->__('Coupon History'),
                'content'   => $this->getLayout()->createBlock('mgxstorebalance_admin/coupon_edit_tab_history')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}