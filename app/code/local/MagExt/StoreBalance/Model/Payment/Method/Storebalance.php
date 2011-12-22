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
 * Store Balance Payment method model
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Payment_Method_Storebalance extends Mage_Payment_Model_Method_Abstract
{
    protected $_code            = 'storebalance';
    protected $_formBlockType   = 'mgxstorebalance/payment_form';
    protected $_canRefund       = false;

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Payment/Model/Method/Mage_Payment_Model_Method_Abstract#isAvailable()
     */
    public function isAvailable($quote=null)
    {
        if ($quote === null || !$quote->getCustomerId())
            return false;

        if (!$this->_getHelper()->isEnabled() || !$this->_checkBalance($quote) || $this->_isRefillItemExists($quote))
        {
            return false;
        }
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Payment/Model/Method/Mage_Payment_Model_Method_Abstract#validate()
     */
    public function validate()
    {
        parent::validate();
        $errorMsg = false;

        if ($this->getInfoInstance() instanceof Mage_Sales_Model_Quote_Payment)
        {
            if (!$this->_checkBalance($this->getInfoInstance()->getQuote()))
                $errorMsg = $this->_getHelper()->__('The Store Balance is not enough to complete this operation.');
        }

        if ($errorMsg)
            Mage::throwException($errorMsg);
        
        return $this;
    }
    
    /**
     * Check if balance amount is enough
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _checkBalance($quote)
    {
        $balance = $this->_getBalanceModel($quote)->getValue();
        return $balance >= $quote->getBaseGrandTotal();
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _isRefillItemExists($quote)
    {
        foreach ($quote->getAllItems() as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getStoreBalanceRefill()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Retrieve Balance model
     * @param Mage_Sales_Model_Quote $quote
     * @return MagExt_StoreBalance_Model_Balance
     */
    protected function _getBalanceModel($quote = null)
    {
        if (!is_null($quote) && $quote->getCustomerId()) {
            $customerId = $quote->getCustomerId();
            $websiteId = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        }
        elseif (!Mage::getSingleton('admin/session')->getUser()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $websiteId  = Mage::app()->getStore()->getWebsiteId();
        }
        else {
            if ($order = Mage::registry('current_order')) {
                $customerId = $order->getCustomerId();
                $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
            }
            elseif ($invoice = Mage::registry('current_invoice')) {
                $customerId = $invoice->getCustomerId();
                $websiteId = Mage::app()->getStore($invoice->getStoreId())->getWebsiteId();
            }
        }
        return Mage::getModel('mgxstorebalance/balance')
            ->setCustomerId($customerId)
            ->setWebsiteId($websiteId)
            ->loadBalance();
    }
    
    /**
     * Retrieve model helper
     *
     * @return MagExt_StoreBalance_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('mgxstorebalance');
    }
}