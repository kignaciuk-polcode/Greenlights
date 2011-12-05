<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Author_View extends Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
{
	/**
	 * Caches and returns the current category
	 *
	 * @return Fishpig_Wordpress_Model_User
	 */
	public function getAuthor()
	{
		if (!$this->hasWordpressAuthor()) {
			$this->setWordpressAuthor(Mage::registry('wordpress_author'));
		}
		
		return $this->getData('wordpress_author');
	}

	/**
	 * Retrieve the Author ID
	 *
	 * @return int
	 */
	public function getAuthorId()
	{
		if ($author = $this->getAuthor()) {
			return $author->getId();
		}
	}
	
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		if (is_null($this->_postCollection)) {
			$this->_postCollection = parent::_getPostCollection()
				->addAuthorIdFilter($this->getAuthorId());
		}
		
		return $this->_postCollection;
	}
}
