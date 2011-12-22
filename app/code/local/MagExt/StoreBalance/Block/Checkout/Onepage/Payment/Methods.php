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
 * Rewrite: One page payment methods block
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
    public function getMethods()
    {
        if (version_compare(Mage::getVersion(), '1.4.2.0', "<")) {
            return parent::getMethods();
        }
        $methods = $this->getData('methods');
        if (is_null($methods)) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = $this->helper('payment')->getStoreMethods($store, $quote);
            $total = $quote->getBaseGrandTotal();
            foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method)
                    && ($total != 0
                        || $method->getCode() == 'free'
                        || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles())
                        || $quote->getPayment()->getMethod() == 'storebalance')) {
                    $this->_assignMethod($method);
                }
                else {
                    unset($methods[$key]);
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }
}