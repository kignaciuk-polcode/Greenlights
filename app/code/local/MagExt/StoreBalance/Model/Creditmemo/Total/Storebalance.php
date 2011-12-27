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
 * Model for collecting totals of Creditmemo and adding Store Balance total row
 *
 * @author  MagExtension Development team
 */ 
class MagExt_StoreBalance_Model_Creditmemo_Total_Storebalance extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Sales/Model/Order/Creditmemo/Total/Mage_Sales_Model_Order_Creditmemo_Total_Abstract#collect()
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        if (!Mage::helper('mgxstorebalance')->isEnabled())
        {
            return $this;
        }
        
        $order = $creditmemo->getOrder();
        if (!$order->getBaseStoreBalanceAmount() || $order->getBaseStoreBalanceInvoiced() == 0)
        {
            return $this;
        }
        
        $invoiceBaseRemainder = $order->getBaseStoreBalanceInvoiced() - $order->getBaseStoreBalanceRefunded();
        $invoiceRemainder     = $order->getStoreBalanceInvoiced() - $order->getStoreBalanceRefunded();
        $used = $baseUsed = 0;
        if ($invoiceBaseRemainder < $creditmemo->getBaseGrandTotal())
        {
            $used = $invoiceRemainder;
            $baseUsed = $invoiceBaseRemainder;
            
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
        }
        else
        {
            $used = $creditmemo->getGrandTotal();
            $baseUsed = $creditmemo->getBaseGrandTotal();
            
            $creditmemo->setBaseGrandTotal(0);
            $creditmemo->setGrandTotal(0);
            $creditmemo->setAllowZeroGrandTotal(true);
        }
        $creditmemo->setStoreBalanceAmount($used);
        $creditmemo->setBaseStoreBalanceAmount($baseUsed);
        
        return $this;
    }
}