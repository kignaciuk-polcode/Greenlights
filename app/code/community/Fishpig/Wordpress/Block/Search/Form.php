<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Search_Form extends Fishpig_Wordpress_Block_Sidebar_Widget_Search
{
	protected function _beforeToHtml()
	{
		Mage::helper('wordpress')->log($this->__('%s has been deprecated; please use %s', get_class($this), get_parent_class($this)));
		
		return parent::_beforeToHtml();
	}
}
