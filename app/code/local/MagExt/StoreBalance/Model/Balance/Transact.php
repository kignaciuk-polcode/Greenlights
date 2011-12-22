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
 * Balance Transaction Model
 *
 * @author  MagExtension Development team
 */ 
class MagExt_StoreBalance_Model_Balance_Transact extends Mage_Core_Model_Abstract
{
    const ACTION_UPDATED     = 0;
    const ACTION_USED        = 1;
    const ACTION_REFUNDED    = 2;
    
    protected function _construct()
    {
        $this->_init('mgxstorebalance/balance_transact');
    }
    
    /**
     * Return available action names
     * @return array action options
     */
    public function getActionOptions()
    {
        return array(
            self::ACTION_UPDATED     => Mage::helper('mgxstorebalance')->__('Modified'),
            self::ACTION_USED        => Mage::helper('mgxstorebalance')->__('Used'),
            self::ACTION_REFUNDED    => Mage::helper('mgxstorebalance')->__('Refunded'),
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Abstract#_beforeSave()
     */
    protected function _beforeSave()
    {
        if (!$this->hasBalanceModel() || !$this->getBalanceModel()->getId())
            Mage::throwException(Mage::helper('mgxstorebalance')->__('Store Balance model hasn\'t assigned.'));

        $this->setBalanceId($this->getBalanceModel()->getId());
        $this->setComment($this->_getComment());
        return parent::_beforeSave();
    }
    
    /**
     * Get comment for transaction
     * @return string comment
     */
    protected function _getComment()
    {
        $comment = '';
        switch ($this->getAction())
        {
            case self::ACTION_UPDATED :
                if ($this->getBalanceModel()->hasStorebalanceCoupon())
                {
                    $comment =  Mage::helper('mgxstorebalance')->__('By Store Balance Coupon %s', $this->getBalanceModel()->getStorebalanceCoupon());
                }
                elseif ($this->getBalanceModel()->hasInvoice())
                {
                    $comment =  Mage::helper('mgxstorebalance')->__('By Invoice #%s (Order #%s)', 
                        $this->getBalanceModel()->getInvoice()->getIncrementId(),
                        $this->getBalanceModel()->getInvoice()->getOrder()->getIncrementId()
                    );
                }
                elseif ($user = Mage::getSingleton('admin/session')->getUser()) 
                {
                    $comment =  $this->getBalanceModel()->getComment();
                }
                break;
            case self::ACTION_USED :
                $this->_checkOrder();
                $comment =  Mage::helper('mgxstorebalance')->__('In Order #%s', $this->getBalanceModel()->getOrder()->getIncrementId());
                break;
            case self::ACTION_REFUNDED :
                $this->_checkCreditmemo();
                $comment =  Mage::helper('mgxstorebalance')->__("Order #%s; \nCreditmemo #%s", 
                    $this->getBalanceModel()->getOrder()->getIncrementId(),
                    $this->getBalanceModel()->getCreditmemo()->getIncrementId());
                break;
            default :
                Mage::throwException(Mage::helper('mgxstorebalance')->__('Unknown transaction action.'));
                break;
        }
        return $comment;
    }
    
    /**
     * Check if creditmemo is set
     */
    protected function _checkCreditmemo()
    {
        if (!$this->getBalanceModel()->getCreditmemo() || !$this->getBalanceModel()->getCreditmemo()->getIncrementId())
        {
            Mage::throwException(Mage::helper('mgxstorebalance')->__('Creditmemo not set.'));
        }
        $this->_checkOrder();
    }
    
    /**
     * Check if order is set
     */
    protected function _checkOrder()
    {
        if (!$this->getBalanceModel()->getOrder() || !$this->getBalanceModel()->getOrder()->getIncrementId())
        {
            Mage::throwException(Mage::helper('mgxstorebalance')->__('Order not set.'));
        }
    }
}