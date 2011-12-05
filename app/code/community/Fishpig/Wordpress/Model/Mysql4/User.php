<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_User extends Fishpig_Wordpress_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/user', 'ID');
	}

	/**
	 * Retrieve a META value for a Post
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return string
	 */	
	public function getMetaValue(Fishpig_Wordpress_Model_User $user, $key, $limit = 1)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(Mage::helper('wordpress/db')->getTableName('usermeta'), 'meta_value')
			->where('user_id=?', $user->getId())
			->where('meta_key=?', $key)
			->limit($limit);

		if ($limit == '1') {
			return $this->_getReadAdapter()->fetchOne($select);
		}
		
		return $this->_getReadAdapter()->fetchCol($select);
	}
	
	/**
     * @deprecated after 2.0.4
     * @see self::getMetaValue()
	 */
	public function getMeta(Fishpig_Wordpress_Model_User $user, $key)
	{
		return $this->getMetaValue($user, $key);
	}
	
	/**
	 * Save a meta value for a user
	 *
	 * @param Fishpig_Wordpress_Model_User $user
	 * @param string $key
	 */
	public function saveMeta(Fishpig_Wordpress_Model_User $user, $key)
	{
		$table = Mage::helper('wordpress')->getTableName('usermeta');
		$select = $this->_getReadAdapter()
			->select()
			->from($table, 'umeta_id')
			->where('meta_key=?', $key)
			->where('user_id=?', $user->getId())
			->limit(1);

		if (($umetaId = $this->_getReadAdapter()->fetchOne($select)) !== false) {
			$this->_getWriteAdapter()->update($table, array('meta_value' => $user->getData($key)), $this->_getWriteAdapter()->quoteInto('umeta_id=?', $umetaId));			
		}
		else {
			$this->_getWriteAdapter()->insert($table, array('user_id' => $user->getId(), 'meta_key' => $key, 'meta_value' => $user->getData($key)));
		}
		
		return $this;
	}
	
	/**
	 * Load the WP User associated with the current logged in Customer
	 *
	 * @param Fishpig_Wordpress_Model_User $user
	 * @return bool
	 */
	public function loadCurrentLoggedInUser(Fishpig_Wordpress_Model_User $user)
	{
		$session = Mage::getSingleton('customer/session');
		
		if ($session->isLoggedIn()) {
			$user->loadByEmail($session->getCustomer()->getEmail());

			return $user->getId() > 0 ? true : false;
		}

		return false;
	}
	
	/**
	 * Save the meta data associated with the user
	 *
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if ($keys = $object->getLoadedMetaKeys()) {
			foreach($keys as $key) {
				if ($object->hasData($key)) {
					$this->saveMeta($object, $key);
				}
			}
		}

		return parent::_afterSave($object);
	}

}
