<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Mysql4_Category_Abstract extends Fishpig_Wordpress_Model_Mysql4_Abstract
{
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('e' => $this->getMainTable()))
			->join(
				array('taxonomy' => Mage::helper('wordpress/db')->getTableName('term_taxonomy')),
				"`e`.`term_id` = `taxonomy`.`term_id`",
				array('category_type' => 'taxonomy', 'description', 'parent')
			)
			->join(
				array('relationships' => Mage::helper('wordpress/db')->getTableName('term_relationships')),
				"`relationships`.`term_taxonomy_id` = `taxonomy`.`term_taxonomy_id`",
				""
			)
			->where("taxonomy.taxonomy = ?", $object->getCategoryType())
			->where("e.{$field}=?", $value)
			->limit(1);
		
		
		return $select;
	}

}
