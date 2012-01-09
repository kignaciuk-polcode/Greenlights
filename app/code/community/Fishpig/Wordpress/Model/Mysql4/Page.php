<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Page extends Fishpig_Wordpress_Model_Mysql4_Post_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/page', 'ID');
	}

	/**
	 * Alter default load select so only pages (and not posts) are returned
	 *
	 * @param string $field
	 * @param string $value
	 * @param Mage_Core_Model_Abstract $object
	 * @return Varien_Db_Select
	*/
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = parent::_getLoadSelect($field, $value, $object)
			->where("`post_type`=?", 'page');

		return $select;
	}

	/**
	 * Retrieve the permalink for a pge
	 *
	 * @param Fishpig_Wordpress_Model_Page $page
	 * @return string
	 */
	public function getPermalink(Fishpig_Wordpress_Model_Page $page)
	{
		$uriParts = array();
		$buffer = $page;
		
		do {
			$uriParts[] = $buffer->getPostName();
			$buffer = $buffer->getParentPage();
		} while($buffer && $buffer->getId());
	
		$parts = count($uriParts);
		
		if ($parts > 1) {
			$uriParts = array_reverse($uriParts);
		}
		
		if ($parts > 0) {
			return Mage::helper('wordpress')->getUrl(implode('/', $uriParts));
		}
		
		return '';
	}
	
	/**
	 * Retrieve a pages parent page
	 *
	 * @param Fishpig_Wordpress_Model_Page $page
	 * @return false|Fishpig_Wordpress_Model_Page
	 */
	public function getParentPage(Fishpig_Wordpress_Model_Page $page)
	{
		if ($page->getPostParent()) {
			$parent = Mage::getModel('wordpress/page')->load($page->getPostParent());
			
			if ($parent->getId()) {
				return $parent;
			}
		}
	
		return false;
	}
	
	/**
	 * Determine whether the given page has any children pages
	 *
	 * @param Fishpig_Wordpress_Model_Page $page
	 * @return bool
	 */
	public function hasChildren(Fishpig_Wordpress_Model_Page $page)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from($this->getMainTable(), 'ID')
			->where('post_parent=?', $page->getId())
			->where('post_type=?', 'page')
			->where('post_status=?', 'publish')
			->limit(1);
			
		return $this->_getReadAdapter()->fetchOne($select) !== false;
	}
}
