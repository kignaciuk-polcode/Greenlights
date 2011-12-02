<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Model_User extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/user');
	}
	
	/**
	 * Store meta keys for retrieves meta values
	 *
	 * @var array
	 */
	protected $_metaKeys = array('first_name', 'last_name', 'wp_capabilities', 'wp_user_level', 'nickname');
	
	public function loadByEmail($email)
	{
		return $this->load($email, 'user_email');
	}
	
	/**
	 * Get the URL for this user
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$slug = preg_replace("/[^a-zA-Z0-9-]/", "-", strtolower($this->getUserLogin()));
		$slug = str_replace('--', '-', $slug);

		return rtrim(Mage::helper('wordpress')->getUrl('author/' . $slug), '/');
	}
	
	/**
     * @deprecated after 2.0.4
     * @see self::getCustomField()
	 */
	public function getMeta($key)
	{
		return $this->getCustomField($key);
	}
	
	/**
	 * Load the WordPress user model associated with the current logged in customer
	 *
	 * @return Fishpig_Wordpress_Model_User
	 */
	public function loadCurrentLoggedInUser()
	{
		return $this->getResource()->loadCurrentLoggedInUser($this);
	}
	
	/**
	 * Retrieve the meta_keys array
	 *
	 * @return array
	 */
	public function getLoadedMetaKeys()
	{
		return $this->_metaKeys;
	}	
	
	/**
	 * Retrieve a custom field value from the database
	 *
	 * @param string $key
	 * @return string
	 */
	public function getCustomField($key)
	{
		if (!$this->hasData($key)) {
			$this->setData($key, $this->getResource()->getMetaValue($this, $key));
		}
		
		return $this->getData($key);	
	}
	
	/**
	 * Load the user\s role
	 *
	 * @return $this
	 */
	protected function _afterLoad()
	{
		if ($roles = $this->getCustomField('wp_capabilities')) {
			$roles = unserialize($roles);
			
			foreach($roles as $role => $enabled) {
				$this->setRole($role);
				break;
			}
		}
			
		return parent::_afterLoad();
	}
}
