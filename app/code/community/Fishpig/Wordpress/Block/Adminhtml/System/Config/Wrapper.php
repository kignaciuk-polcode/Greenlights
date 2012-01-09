<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_System_Config_Wrapper extends Fishpig_Wordpress_Block_Adminhtml_Template 
{
	/**
	 * Used to ensure ACL message is only displayed once
	 *
	 * @var int
	 */
	static protected $_aclNoticeIssued = 0;
	
	/**
	 * Message to display when ACL isn't correct
	 *
	 * @var const string
	 */
	const _ACTION_MSG = 'To finish installing Fishpig\'s Magento WordPress integration extension, log out of Magento and log back in.';
	
	/**
	 * This adds a message to the settings page if the 'Access denied' message is displayed
	 */
	public function __construct()
	{
		parent::__construct();
		
		if (!$this->isAclValid()) {
			if (self::$_aclNoticeIssued < 2) {
				Mage::getSingleton('adminhtml/session')->addNotice(self::_ACTION_MSG);
				self::$_aclNoticeIssued = (self::$_aclNoticeIssued + 1);		
			}
		}
	}
	
	/**
	 * Returns true if the ACL is registered correctly
	 *
	 * @return bool
	 */
	public function isAclValid()
	{
		return Mage::helper('wordpress/system')->isAclValid();
	}	
}
