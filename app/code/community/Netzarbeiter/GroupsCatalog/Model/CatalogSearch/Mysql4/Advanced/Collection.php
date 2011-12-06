<?php

class Netzarbeiter_GroupsCatalog_Model_CatalogSearch_Mysql4_Advanced_Collection extends Mage_CatalogSearch_Model_Mysql4_Advanced_Collection
{
	/**
	 * Fix the result count display for the advanced search
	 *
	 * @return Varien_Db_Select
	 */
	public function  getSelectCountSql()
	{
		Mage::helper('groupscatalog')->addGroupsFilterToProductCollection($this);
		return parent::getSelectCountSql();
	}
}
