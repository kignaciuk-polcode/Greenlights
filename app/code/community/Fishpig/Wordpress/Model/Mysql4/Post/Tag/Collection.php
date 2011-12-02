<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post_Tag_Collection extends Fishpig_Wordpress_Model_Mysql4_Category_Collection_Abstract
{
	/**
	 * Defines the type of category
	 * Can be either category or link_category
	 *
	 * @var string
	 */
	protected $_categoryType = 'post_tag';
	
	public function _construct()
	{
		$this->_init('wordpress/post_tag');
	}
	
	/**
	 * Filter the collection so that only tags in the cloud
	 * are returned
	 *
	 */
	public function addTagCloudFilter()
	{
		$this->addFieldToFilter('main_table.term_id', array('in' => Mage::getResourceModel('wordpress/post_tag')->getCloudTagIds()));
		
		return $this;
	}
}
