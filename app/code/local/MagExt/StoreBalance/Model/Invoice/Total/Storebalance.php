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
 * Model for collecting totals of Invoice and adding Store Balance total row
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Invoice_Total_Storebalance extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Sales/Model/Order/Invoice/Total/Mage_Sales_Model_Order_Invoice_Total_Abstract#collect()
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        if (!Mage::helper('mgxstorebalance')->isEnabled())
        {
            return $this;
        }
        $order = $invoice->getOrder();
        if (!$order->getBaseStoreBalanceAmount() || $order->getBaseStoreBalanceAmount() == $order->getBaseStoreBalanceInvoiced())
        {
            return $this;
        }
        
        $invoiceBaseRemainder = $order->getBaseStoreBalanceAmount() - $order->getBaseStoreBalanceInvoiced();
        $invoiceRemainder     = $order->getStoreBalanceAmount() - $order->getStoreBalanceInvoiced();
        $used = $baseUsed = 0;
        if ($invoiceBaseRemainder < $invoice->getBaseGrandTotal())
        {
            $used = $invoiceRemainder;
            $baseUsed = $invoiceBaseRemainder;
            
            $invoice->setGrandTotal($invoice->getGrandTotal()-$used);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
        }
        else {
        	$used = $invoice->getGrandTotal();
            $baseUsed = $invoice->getBaseGrandTotal();
            
            $invoice->setBaseGrandTotal(0);
            $invoice->setGrandTotal(0);
        }
        
        $invoice->setStoreBalanceAmount($used);
        $invoice->setBaseStoreBalanceAmount($baseUsed);
        
        return $this;
    }
}