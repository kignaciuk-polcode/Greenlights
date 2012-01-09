<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List_Comment extends Fishpig_Wordpress_Block_Sidebar_Widget_Comments
{
	protected function _beforeToHtml()
	{
		Mage::helper('wordpress')->log($this->__('%s has been deprecated; please use %s', get_class($this), get_parent_class($this)));

		$this->setTemplate('wordpress/sidebar/widget/comments.phtml');
		$this->setTitle($this->__('Recent Comments'));
		
		return parent::_beforeToHtml();
	}
}
