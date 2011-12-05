<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Template
{
	/**
	 * Returns true if this is the WordPress section
	 *
	 * @return bool
	 */
	public function isWordpressSection()
	{
		return strtolower(trim($this->getParam('section'))) == 'wordpress';
	}
	
	/**
	 * Returns true if this is the WordPress Blog section
	 *
	 * @return bool
	 */
	public function isWordpressBlogSection()
	{
		return strtolower(trim($this->getParam('section'))) == 'wordpress_blog';
	}

	/**
	 * Gets the Adminhtml session
	 * @return Mage_Adminhtml_Model_Session
	 */
	public function getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}
	
	public function getParam($param, $default = null)
	{
		return Mage::app()->getRequest()->getParam($param, $default);
	}
	
	public function getModuleName()
	{
		return parent::getModuleName() . '_Adminhtml';
	}
}
