<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Admin_User extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/admin_user', 'autologin_id');
	}

	/**
	 * Custom load SQL
	 *
	 * @param string $field - field to match $value to
	 * @param string|int $value - $value to load record based on
	 * @param Mage_Core_Model_Abstract $object - object we're trying to load to
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('e' => $this->getMainTable()))
			->where("e.{$field}=?", $value)
			->where('user_id=?', Mage::getSingleton('admin/session')->getUser()->getId())
			->limit(1);

		return $select;
	}
	
	protected function _getWriteAdapter()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_write');
	}
	
	public function  _getReadAdapter()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
}