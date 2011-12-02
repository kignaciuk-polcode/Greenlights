<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Page extends Fishpig_Wordpress_Model_Post_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/page');
	}

	/**
	 * Retrieve the permalink for the page
	 *
	 * @return false|string
	 */
	public function getPermalink()
	{
		if (!$this->hasPermalink()) {
			$this->setPermalink($this->getResource()->getPermalink($this));
		}
		
		return $this->getData('permalink');
	}
	
	/**
	 * Retrieve the pages permalink
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->getPermalink();
	}
	
	/**
	 * Retrieve the parent page
	 *
	 * @return false|Fishpig_Wordpress_Model_Page
	 */
	public function getParentPage()
	{
		if (!$this->hasParentPage()) {
			$this->setParentPage($this->getResource()->getParentPage($this));
		}
		
		return $this->getData('parent_page');
	}

	/**
	 * Retrieve the root page in the current branch
	 *
	 * @return false|Fishpig_Wordpress_Model_Page
	 */
	public function getRootPage()
	{
		$parent = $this->getParentPage();

		while($parent->getParentPage()) {
			$parent = $parent->getParentPage();
		}

		return $parent;
	}
	
	/**
	 * Retrieve the page's children pages
	 *
	 * @return Fishpig_Wordpress_Model_Mysql_Page_Collection
	 */
	public function getChildrenPages()
	{
		if (!$this->hasChildrenPages()) {
			$this->setChildrenPages($this->getResource()->getChildrenPages($this));
		}
		
		return $this->getData('children_pages');
	}
	
	/**
	  * Determine whether the page has children
	  * Doesn't load a collection, instead performs simple SQL query for efficiency
	  *
	  * @return bool
	  */
	public function hasChildren()
	{
		if (!$this->hasHasChildren()) {
			$this->setHasChildren($this->getResource()->hasChildren($this));
		}
		
		return $this->getData('has_children');
	}
	
	/**
	 * Retrieves the custom menu label set using All In One SEO
	 * If not set, returns post title
	 *
	 * @return string
	 */
	public function getMenuLabel()
	{
		if ($menuLabel = $this->getCustomField('_aioseop_menulabel')) {
			return $menuLabel;
		}
		
		return $this->getPostTitle();
	}
}
