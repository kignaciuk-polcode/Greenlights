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
 * Model for collecting totals of Quote and adding Store Balance total row
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Quote_Total_Storebalance extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('mgxstorebalance');
    }
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Sales/Model/Quote/Address/Total/Mage_Sales_Model_Quote_Address_Total_Abstract#collect()
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('mgxstorebalance')->isEnabled())
        {
            return $this;
        }
        $quote = $address->getQuote();
        if (!($quote->getCustomer()->getId() && $quote->getPayment()->getMethod() == 'storebalance'))
        {
            $quote->setBaseStoreBalanceTotal(0);
            $quote->setStoreBalanceTotal(0);
            $address->setBaseStoreBalanceAmount(NULL);
            $address->setStoreBalanceAmount(NULL);
            return $this;
        }
        if (!$quote->getStoreBalanceTotalsCollected())
        {
            $quote->setStoreBalanceTotalsCollected(true);
            $quote->setBaseStoreBalanceTotal(0);
            $quote->setStoreBalanceTotal(0);
        }
        
        $store = Mage::app()->getStore($quote->getStoreId());
        $baseBalance = Mage::getModel('mgxstorebalance/balance')
            ->setCustomer($quote->getCustomer())
            ->setWebsiteId($store->getWebsiteId())
            ->loadBalance()
            ->getValue();
        $balance = $quote->getStore()->convertPrice($baseBalance);
        
        $baseBalanceLeft = $baseBalance - $quote->getBaseStoreBalanceTotal();
        $balanceLeft     = $balance - $quote->getStoreBalanceTotal();
        
        if ($baseBalanceLeft < $address->getBaseGrandTotal())
        {
            //The Store Balance is not enough to complete this operation
            return $this;
        }
        
        //set quote totals
        $quote->setBaseStoreBalanceTotal($address->getBaseGrandTotal() + $quote->getBaseStoreBalanceTotal());
        $quote->setStoreBalanceTotal($address->getGrandTotal() + $quote->getStoreBalanceTotal());
        //set store balance amount to address
        $address->setBaseStoreBalanceAmount($address->getBaseGrandTotal());
        $address->setStoreBalanceAmount($address->getGrandTotal());
        
        $address->setBaseGrandTotal(0);
        $address->setGrandTotal(0);
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Sales/Model/Quote/Address/Total/Mage_Sales_Model_Quote_Address_Total_Abstract#fetch()
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('mgxstorebalance')->isEnabled())
        {
            return $this;
        }
        
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());
        $baseBalanceLeft = 0;
        if ($quote->getCustomer()->getId()) {
        $baseBalance = Mage::getModel('mgxstorebalance/balance')
            ->setCustomer($quote->getCustomer())
            ->setWebsiteId($store->getWebsiteId())
            ->loadBalance()
            ->getValue();
        
        $baseBalanceLeft = $baseBalance - $quote->getBaseStoreBalanceTotal();
        }
        
        if ($address->getStoreBalanceAmount() && ($baseBalanceLeft >= $address->getBaseGrandTotal())) {
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>Mage::helper('mgxstorebalance')->__('Store Balance'),
                'value'=>-$address->getStoreBalanceAmount(),
            ));
        }
        return $this;
    }
}