<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Link_Category extends Fishpig_Wordpress_Model_Category_Abstract
{
	protected $_categoryType = 'link_category';

	public function _construct()
	{
		$this->_init('wordpress/link_category');
	}
	
	public function getLinks()
	{
		return Mage::getResourceModel('wordpress/link_collection')
			->addCategoryIdFilter($this->getId());
	}
	
	public function getPostCollection()
	{
	
	}
}
