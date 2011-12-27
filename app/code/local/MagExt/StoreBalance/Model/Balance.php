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
 * Balance Model
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Balance extends Mage_Core_Model_Abstract
{
	protected $_eventPrefix	= 'mgxstorebalance_balance';
	protected $_eventObject  = 'balance';
	
	protected function _construct()
	{
		$this->_init('mgxstorebalance/balance');
	}
	
	public function loadBalance()
	{
	    $this->_prepare();
	    return $this;
	}
	
	/**
	 * Return Balance value
	 * @return float
	 */
	public function getValue()
	{
	    return (float)$this->getData('value');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Abstract#_beforeSave()
	 */
	protected function _beforeSave()
	{
	    $this->_prepare();
	    
	    //set transaction data
        if ($this->hasCreditmemo())
        {
            $this->getTransactModel()->setAction(MagExt_StoreBalance_Model_Balance_Transact::ACTION_REFUNDED);
        }
	    elseif ($this->hasOrder())
	    {
	        $this->getTransactModel()->setAction(MagExt_StoreBalance_Model_Balance_Transact::ACTION_USED);
	    }
	    
	    if (!$this->getTransactModel()->hasAction())
        {
            $this->getTransactModel()->setAction(MagExt_StoreBalance_Model_Balance_Transact::ACTION_UPDATED);
        }
        $this->getTransactModel()->setValue($this->getValue());
        $this->getTransactModel()->setValueChange($this->getValueChange());
        return parent::_beforeSave();
	}
	
	/**
	 * Validate and prepare data before save 
	 * @return MagExt_StoreBalance_Model_Balance
	 */
	protected function _prepare()
	{
	    //validate customer
	    if (!$this->getCustomerId())
	    {
	        if ($this->getCustomer() && $this->getCustomer()->getId())
                $this->setCustomerId($this->getCustomer()->getId());  
	    }
	    if (!$this->getCustomerId())
	    {
	        Mage::throwException(Mage::helper('mgxstorebalance')->__('Customer ID is not set'));
	    }
	    
	    //validate website
	    if (!$this->getWebsiteId())
	    {
	        if (!Mage::app()->getStore()->isAdmin())
	           $this->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
	    }
        if (!$this->getWebsiteId())
	    {
	        Mage::throwException(Mage::helper('mgxstorebalance')->__('Website ID is not set'));
	    }

	    //load balance data
	    $this->getResource()->loadByCustomerAndWebsite($this, $this->getCustomerId(), $this->getWebsiteId());

	    //validate balance value
	    if ($this->hasValueChange()) 
	    {
            $value = (float)$this->getValue();
	        $add = (float)$this->getValueChange();
	        if ($value + $add < 0)
	        {
	            $add = -1 * $value;
	        }
	        $this->setValueChange($add);
            $this->setValue($value + $add);
	    }
	    
	    return $this;
	}
	
	/**
	 * Refill the store balance using Store Balance Coupon
	 * Method should be called before changing coupon value and saving
	 * @var MagExt_StoreBalance_Model_Coupon $coupon
	 * @return MagExt_StoreBalance_Model_Balance
	 */
	public function refill($coupon)
	{
	    $this->setValueChange($coupon->getBalance())
	         ->setStorebalanceCoupon($coupon->getHash())
	         ->setCustomerId($coupon->getCustomerId())
	         ->setWebsiteId($coupon->getWebsiteId())
	         ->save();
	    return $this;
	}
	
    /**
     * Refill the balance after invoicing special product
     * Method calls after saving invoice
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param int $balance Balance amount
     * @return MagExt_StoreBalance_Model_Balance
     */
    public function refillByInvoice($invoice, $balance)
    {
        if ($invoice->getCustomerId())
        {
            $this->setValueChange($balance)
                 ->setInvoice($invoice)
                 ->setCustomerId($invoice->getCustomerId())
                 ->setWebsiteId(Mage::app()->getStore($invoice->getStoreId())->getWebsiteId())
                 ->save();
        }
        return $this;
    }
	
	/**
	 * Use store balance to purchase order. Decrease balance
	 * @param Mage_Sales_Model_Order $order
	 * @return MagExt_StoreBalance_Model_Balance
	 */
	public function useBalance($order)
	{
	    $this->setValueChange(-$order->getBaseStoreBalanceAmount())
	       ->setOrder($order)
	       ->setCustomerId($order->getCustomerId())
	       ->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
	       ->save();
        return $this;
	}
	
	/**
	 * Return ordered amount to store balance after order refunding
	 * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
	 * @return MagExt_StoreBalance_Model_Balance
	 */
	public function refund($creditmemo)
	{
	    $this->setValueChange($creditmemo->getBaseStoreBalanceAmount())
           ->setCreditmemo($creditmemo)
           ->setOrder($creditmemo->getOrder())
           ->setCustomerId($creditmemo->getOrder()->getCustomerId())
           ->setWebsiteId(Mage::app()->getStore($creditmemo->getOrder()->getStoreId())->getWebsiteId())
           ->save();
	    return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Abstract#_afterSave()
	 */
	protected function _afterSave()
	{
	    parent::_afterSave();
	    $this->getTransactModel()->unsetData();
	    return $this;
	}
	
	/**
     * Retreive transaction model instance
     *
     * @return MagExt_StoreBalance_Model_Balance_Transact
     */
    public function getTransactModel()
    {
        if (!$this->hasData('transact_model'))
        {
            $this->setTransactModel(Mage::getModel('mgxstorebalance/balance_transact'));
        }
        return $this->getData('transact_model');
    }
}