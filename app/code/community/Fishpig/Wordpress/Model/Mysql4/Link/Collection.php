<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Link_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/link');
	}

	public function addCategoryIdFilter($categoryId)
	{
		$tbl_tax = Mage::helper('wordpress/db')->getTableName('term_taxonomy');
		$tbl_rel = Mage::helper('wordpress/db')->getTableName('term_relationships');
		
		$this->getSelect()->join($tbl_rel, "`$tbl_rel`.`object_id` = `main_table`.`link_id`", '');
		$this->getSelect()->join($tbl_tax, "`$tbl_tax`.`term_taxonomy_id` = `$tbl_rel`.`term_taxonomy_id` AND `$tbl_tax`.`term_id` = $categoryId AND `$tbl_tax`.`taxonomy` = 'link_category'", '');
		return $this;
	}
}
