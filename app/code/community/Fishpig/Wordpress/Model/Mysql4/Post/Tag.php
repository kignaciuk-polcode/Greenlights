<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post_Tag extends Fishpig_Wordpress_Model_Mysql4_Category_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post_tag', 'term_id');
	}
	
	/**
	 * Retrieve an array of ID's to be used in the tag cloud
	 *
	 * @return array|false
	 */
	public function getCloudTagIds()
	{
		$tags = Mage::getResourceModel('wordpress/post_tag_collection')
			->addOrderByCount();
			
		if ($maxTagsToDisplay = Mage::getStoreConfig('wordpress_blog/tag_cloud/max_tags_to_display')) {
			$tags->getSelect()->limit($maxTagsToDisplay);
		}
		
		if (method_exists($tags->getSelect(), 'setPart')) {
			$tags->getSelect()->setPart('columns', array());
			$tags->getSelect()->columns(array('main_table.term_id'));		

			return $this->_getReadAdapter()->fetchCol($tags->getSelect());
		}

		/**
		 * Magento 1.3.0.0 compatibility
		 *
		 */
		if ($results = $this->_getReadAdapter()->fetchAll($tags->getSelect())) {
			$tagIds = array();
			
			foreach($results as $result) {
				$tagIds[] = $result['term_id'];
			}
			
			return $tagIds;
		}
		
		return false;
	}
}
