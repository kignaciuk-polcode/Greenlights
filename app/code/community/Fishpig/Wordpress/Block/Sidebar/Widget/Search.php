<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar_Widget_Search extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Retrieve the action URL for the search form
	 *
	 * @return string
	 */
	public function getFormActionUrl()
	{
		return $this->helper('wordpress')->getUrl($this->helper('wordpress/search')->getSearchRoute()) . '/';
	}
	
	/**
	 * Retrieve the default title
	 *
	 * @return string
	 */
	public function getDefaultTitle()
	{
		return $this->__('Blog Search');
	}
}
