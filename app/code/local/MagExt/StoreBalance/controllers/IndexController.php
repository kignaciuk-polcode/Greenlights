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
 * Front-End Index controller
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Controller/Front/Mage_Core_Controller_Front_Action#preDispatch()
     */
	public function preDispatch()
	{
		parent::preDispatch();
		
		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        
		if (!Mage::helper('mgxstorebalance')->isEnabled()) {
            $this->norouteAction();
            return;
        }
        return $this;
	}
	
	/**
	 * Index action
	 */
	public function indexAction()
	{
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->loadLayoutUpdates();
        
        $data = Mage::getSingleton('customer/session')->getStoreBalanceFormData(true);
        Mage::register('storebalance_coupon', new Varien_Object());
        if (!empty($data)) {
            Mage::registry('storebalance_coupon')->addData($data);
        }
        
        $this->getLayout()->getBlock('head')->setTitle('Store Balance');
        $this->renderLayout();
	}
	
	/**
	 * Display transaction's grid
	 */
    public function transactAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        $this->getLayout()->getBlock('head')->setTitle('Store Balance Transactions');
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
	
    /**
     * Refill store balance
     */
	public function refillAction()
	{
	    if (!Mage::helper('mgxstorebalance')->isEnabledCoupons())
	       $this->_forward('index');
	    if ($this->getRequest()->has('storebalance_coupon'))
	    {
	        $coupon = $this->getRequest()->getPost('storebalance_coupon');
	        try 
	        {
                $couponModel = Mage::getModel('mgxstorebalance/coupon')->loadByHash($coupon);
                $couponModel->useCoupon();
               
                Mage::getSingleton('customer/session')->addSuccess($this->__('Store Balance refilled'));
	        }
	        catch (Mage_Core_Exception $e)
	        {
    	            Mage::getSingleton('customer/session')->setStoreBalanceFormData($this->getRequest()->getPost())
	               ->addError($e->getMessage());
	        }
	        catch (Exception $e)
	        {
	            Mage::getSingleton('customer/session')->addException($e, $this->__('Error occur while refilling the balance.'));
	        }
	        $this->_redirect('*');
	    }
	}
}