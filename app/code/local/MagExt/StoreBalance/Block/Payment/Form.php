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
 * Additional form for payment method "Store balance"
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mgxstorebalance/payment/form.phtml');
    }
    
    public function getBalance()
    {
        if (Mage::getSingleton('admin/session')->getUser()) {
            if ($session = Mage::getSingleton('adminhtml/session_quote')) {
                $customerId = $session->getCustomerId();
                $websiteId  = Mage::app()->getStore($session->getStoreId())->getWebsiteId();
            }
            else 
                return 0;
        }
        else {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $websiteId  = Mage::app()->getStore()->getWebsiteId();
        }
        return Mage::getModel('mgxstorebalance/balance')
            ->setCustomerId($customerId)
            ->setWebsiteId($websiteId)
            ->loadBalance()
            ->getValue();
    }
} 