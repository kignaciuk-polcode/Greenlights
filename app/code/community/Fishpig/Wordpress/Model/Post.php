<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Model_Post extends Fishpig_Wordpress_Model_Post_Abstract
{
	/**
	 * Tag used to identify where to break the post content up for excerpt
	 *
	 * @var const string
	 */
	const TEASER_TAG = '<!--more-->';

	public function _construct()
	{
		$this->_init('wordpress/post');
	}

	/**
	 * Returns the permalink used to access this post
	 *
	 * @return string
	 */
	public function getPermalink()
	{
		if (!$this->hasData('permalink')) {
			$this->setData('permalink', Mage::helper('wordpress/post')->getPermalink($this));
		}
		
		return $this->getData('permalink');
	}

	/**
	 * Wrapper for self::getPermalink()
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->getPermalink();
	}
	
	/**
	 * Retrieve the post excerpt
	 * If no excerpt, try to shorten the post_content field
	 *
	 * @return string
	 */
	public function getPostExcerpt($includeSuffix = true)
	{
		if (!$this->getData('post_excerpt')) {
			if (strpos($this->getPostContent(), self::TEASER_TAG) !== false) {
				$excerpt = $this->_getPostTeaser($includeSuffix);
			}
			else {
				$excerpt = $this->_getAutoGeneratedExcerpt($includeSuffix);
			}

			$this->setPostExcerpt(Mage::helper('wordpress/filter')->applyFilters($excerpt, array('id' => $this->getId(), 'type' => 'post', 'filters' => 'excerpt')));
		}			

		return $this->getData('post_excerpt');
	}
	
	/**
	 * Retrieve the post teaser
	 * This is the data from the post_content field upto to the TEASER_TAG
	 *
	 * @return string
	 */
	protected function _getPostTeaser($includeSuffix = true)
	{
		if (strpos($this->getPostContent(), self::TEASER_TAG) !== false) {
			$content = $this->getPostContent();
			
			$excerpt = substr($content, 0, strpos($content, self::TEASER_TAG));
			
			if ($includeSuffix && $this->_getTeaserAnchor()) {
				$excerpt .= sprintf(' <a href="%s" class="read-more">%s</a>', $this->getPermalink(), $this->_getTeaserAnchor());
			}
			
			return $excerpt;
		}
		
		return null;
	}
	
	/**
	 * Retrieve the auto generated excerpt
	 *
	 * @return string
	 */
	protected function _getAutoGeneratedExcerpt($includeSuffix = true)
	{
		$content = explode(' ', strip_tags($this->getPostContent()));

		if (count($content) > $this->_getExcerptSize()) {
			$excerpt = implode(' ', array_splice($content, 0, $this->_getExcerptSize()));
			$excerpt = rtrim($excerpt, " .,!/\\:;-_@%&*\"'\r\n\t");
			
			if ($includeSuffix) {
				$excerpt .= Mage::getStoreConfig('wordpress_blog/posts/excerpt_suffix');
			}
			
			return $excerpt;
		}

		return $this->getPostContent();
	}

	/**
	 * Returns the parent category of the current post
	 *
	 * @return Fishpig_Wordpress_Model_Post_Category
	 */
	public function getParentCategory()
	{
		if (!$this->hasData('parent_category')) {
			$this->setData('parent_category', $this->getParentCategories()->getFirstItem());
		}
		
		return $this->getData('parent_category');
	}
	
	/**
	 * Retrieve a collection of all parent categories
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Category_Collection
	 */
	public function getParentCategories()
	{
		if (!$this->hasData('parent_categories')) {
			$this->setData('parent_categories', $this->getResource()->getParentCategories($this));
		}
		
		return $this->getData('parent_categories');
	}

	/**
	 * Gets a collection of post tags
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Tag_Collection
	 */
	public function getTags()
	{
		if (!$this->hasData('tags')) {
			$this->setData('tags', $this->getResource()->getPostTags($this));
		}
		
		return $this->getData('tags');
	}

	/**
	 * Retrieve the read more anchor text
	 *
	 * @return string|false
	 */
	protected function _getTeaserAnchor()
	{
		$teaserAnchor = trim(Mage::helper('wordpress')->htmlEscape(Mage::getStoreConfig('wordpress_blog/posts/more_anchor')));
		
		return $teaserAnchor ? $teaserAnchor : false;
	}
	
	/**
	 * Retrieve the amount of words to use in the auto-generated excerpt
	 *
	 * @return int
	 */
	protected function _getExcerptSize()
	{
		return (int)Mage::getStoreConfig('wordpress_blog/posts/excerpt_size');
	}
}