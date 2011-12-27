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
 * Store Balance Coupon Resource Model
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Mysql4_Coupon extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('mgxstorebalance/coupon', 'coupon_id');
	}
	
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
        $date = Mage::app()->getLocale()->date();
        $dateFull = clone $date;
        $date->setHour(0)
            ->setMinute(0)
            ->setSecond(0);
        if (!$object->getFromDate()) {
            $object->setFromDate($date);
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        
        if (!$object->getId()) {
            $object->setCreatedDate($dateFull);
        }
        if ($object->getCreatedDate() instanceof Zend_Date) {
            $object->setCreatedDate($object->getCreatedDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
        $object->setUpdatedDate($dateFull);
        if ($object->getUpdatedDate() instanceof Zend_Date) {
            $object->setUpdatedDate($object->getUpdatedDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
        return parent::_beforeSave($object);
	}
}