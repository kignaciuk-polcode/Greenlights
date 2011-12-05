<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Admin_User extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/admin_user');
	}
	
	/**
	 * Decrypts encrypted details once they are loaded from DB
	 *
	 */
	protected function _afterLoad()
	{
		parent::_afterLoad();
		
		foreach( array('username', 'password') as $field) {
			$this->setData($field, $this->decrypt($this->getData($field)));
		}
		
		return $this;
	}
	
	/**
	 * Encrypts information before saving to DB
	 *
	 */
	protected function _beforeSave()
	{
		foreach( array('username', 'password') as $field) {
			$this->setData($field, $this->encrypt($this->getData($field)));
		}
		
		return parent::_beforeSave();
	}
	
	/**
	 * Encrypt's a value
	 * This chooses the correct encryption model for each Magento version
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function encrypt($value)
	{
		if (Mage::helper('wordpress')->isEnterpriseMagento()) {
			return Mage::getSingleton('core/encryption')->encrypt($value);
		}
	
		return Mage::helper('core')->encrypt($value);
	}
	
	/**
	 * Decrypt's a value
	 * This chooses the correct encryption model for each Magento version
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function decrypt($value)
	{
		if (Mage::helper('wordpress')->isEnterpriseMagento()) {
			return Mage::getSingleton('core/encryption')->decrypt($value);
		}
	
		return Mage::helper('core')->decrypt($value);
	}
}
