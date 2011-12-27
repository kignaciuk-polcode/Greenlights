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
 * Coupon history model
 *
 * @author  MagExtension Development team
 */ 
class MagExt_StoreBalance_Model_Coupon_History extends Mage_Core_Model_Abstract
{
    const ACTION_CREATED = 0;
    const ACTION_UPDATED = 1;
    const ACTION_USED    = 2;
    
    protected function _construct()
    {
        $this->_init('mgxstorebalance/coupon_history');
    }
    
    /**
     * Return available action names
     * @return array action options
     */
    public function getActionOptions()
    {
        return array(
            self::ACTION_CREATED => Mage::helper('mgxstorebalance')->__('Created'),
            self::ACTION_UPDATED => Mage::helper('mgxstorebalance')->__('Updated'),
            self::ACTION_USED    => Mage::helper('mgxstorebalance')->__('Used'),
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Abstract#_beforeSave()
     */
    protected function _beforeSave()
    {
        if (!$this->hasCouponModel())
            Mage::throwException(Mage::helper('mgxstorebalance')->__('Coupon hasn\'t assigned.'));

        $this->setCouponId($this->getCouponModel()->getId());
        $this->setComment($this->_getComment());
        return parent::_beforeSave();
    }
    
    /**
     * Get comment for history
     * @return string comment
     */
    protected function _getComment()
    {
        $comment = '';
        switch ($this->getAction())
        {
            case self::ACTION_CREATED :
            case self::ACTION_UPDATED :

                break;
            case self::ACTION_USED :
                if ($customerId = $this->getCouponModel()->getCustomerId()) {
                    $comment =  Mage::helper('mgxstorebalance')->__('By Customer #%s', $customerId);
                }
                break;
            default :
                Mage::throwException(Mage::helper('mgxstorebalance')->__('Unknown history action.'));
                break;
        }
        return $comment;
    }
}