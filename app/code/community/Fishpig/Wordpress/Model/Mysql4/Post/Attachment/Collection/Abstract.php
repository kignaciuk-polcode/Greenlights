<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post_Attachment_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function load($printQuery = false, $logQuery = false)
    {
		$this->getSelect()
			->where("post_type = ?", 'attachment')
			->where("post_mime_type LIKE 'image%'");
			
		return parent::load($printQuery, $logQuery);
    }
	
	public function setParent($parentId = 0)
	{
		$this->getSelect()->where("post_parent = ?", $parentId);
		return $this;
	}
	
	protected function _afterLoad()
	{
		parent::_afterLoad();
		
		
		foreach($this->_items as $item) {
			$item->loadSerializedData();
		}
		
		return $this;
	}
}
