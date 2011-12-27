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
 * Balance Resource Model
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Mysql4_Balance extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('mgxstorebalance/storebalance', 'balance_id');
	}
	
	public function loadByCustomerAndWebsite($object, $customerId, $websiteId)
	{
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->where('customer_id = ?', $customerId)
                ->where('website_id = ?', $websiteId)
                ->limit(1);
            $data = $read->fetchRow($select);
            if ($data) {
                $object->addData($data);
            }
        }
	}
}