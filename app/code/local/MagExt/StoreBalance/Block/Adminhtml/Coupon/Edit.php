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
 * Coupon form container
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Coupon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
    {
    	$this->_controller = 'adminhtml_coupon';
		$this->_blockGroup = 'mgxstorebalance';
        parent::__construct();
        if ($this->getCoupon()->getIsNew())
            $this->_updateButton('save', 'label', Mage::helper('mgxstorebalance')->__('Generate'));
    }
    
    /**
     * Get current Store Balance Coupon
     * @return MagExt_StoreBalance_Model_Coupon
     */
    public function getCoupon()
    {
    	return Mage::registry('current_storebalance_coupon');
    }
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Adminhtml/Block/Widget/Mage_Adminhtml_Block_Widget_Container#getHeaderText()
     */
    public function getHeaderText()
    {
    	if (!$this->getCoupon()->getIsNew()) {
            return Mage::helper('mgxstorebalance')->__('Edit Store Balance Coupon: %s', $this->htmlEscape($this->getCoupon()->getHash()));
        }
        else {
            return Mage::helper('mgxstorebalance')->__('Generate Store Balance Coupons');
        }
    }
}