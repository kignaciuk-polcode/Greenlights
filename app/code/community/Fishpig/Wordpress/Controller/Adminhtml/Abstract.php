<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_action
{
	/**
	 * Gets the Adminhtml session
	 * @return Mage_Adminhtml_Model_Session
	 */
	public function getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}
	
	/**
	 * Retrieve an auto-login model
	 *
	 * @return Fishpig_Wordpress_Model_Admin_User
	 */
	public function getAutoLogin()
	{
		return Mage::getModel('wordpress/admin_user')->load(0, 'store_id');
	}
	
	/**
	 * Redirect the user to the WordPress Config area
	 *
	 */
	public function redirectToWordpressConfig()
	{
		$this->_redirect($this->getWordpressConfigPath());
		return;
	}
	
	/**
	 * Retrieve the URI path to WordPress config
	 *
	 * @return string
	 */
	public function getWordpressConfigPath()
	{
		return 'adminhtml/system_config/edit/section/wordpress';
	}

	/**
	 * Add an error to the session
	 *
	 * @param string $msg
	 */	
	public function addError($msg)
	{
		return Mage::getSingleton('adminhtml/session')->addError($msg);
	}
	
	/**
	 * Add a notice to the session
	 *
	 * @param string $msg
	 */
	public function addNotice($msg)
	{
		return Mage::getSingleton('adminhtml/session')->addNotice($msg);
	}
}

