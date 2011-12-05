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
}

