<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Link_List extends Mage_Core_Block_Template
{
	/**
	 * Returns a collection of links
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Link_Collection
	 */
	public function getLinks()
	{
		if (!$this->hasLinks()) {
			$collection = Mage::getResourceModel('wordpress/link_collection');
			
			if ($this->hasLinkCategoryId()) {
				$collection->addCategoryIdFilter($this->getLinkCategoryId());
			}
			
			$collection->getSelect()->order('link_name ASC');

			$this->setLinks($collection);
		}
		
		return $this->getData('links');
	}
}
