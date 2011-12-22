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
 * Coupon grid container
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Coupon extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_coupon';
		$this->_blockGroup = 'mgxstorebalance';
		$this->_headerText = Mage::helper('mgxstorebalance')->__('Manage Store Balance Coupons');
		$this->_addButtonLabel = Mage::helper('mgxstorebalance')->__('New Store Balance Coupon(s)');
		
		parent::__construct();
	}
}