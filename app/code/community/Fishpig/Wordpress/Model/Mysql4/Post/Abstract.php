<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Mysql4_Post_Abstract extends Fishpig_Wordpress_Model_Mysql4_Abstract
{
	/**
	 * Custom load SQL
	 *
	 * @param string $field - field to match $value to
	 * @param string|int $value - $value to load record based on
	 * @param Mage_Core_Model_Abstract $object - object we're trying to load to
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('e' => $this->getMainTable()))
			->where("e.{$field}=?", $value);

		$select->limit(1);

		return $select;
	}
	
	/**
	 * Retrieve a META description for a Post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return string
	 */
	public function getMetaDescription(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		if (Mage::helper('wordpress/plugin_allInOneSeo')->isEnabled()) {
			$desc = trim($this->getMetaValue($post, '_aioseop_description'));
		
			return $desc ? $desc : strip_tags($post->getPostExcerpt(false));
		}
		
		return strip_tags($post->getPostExcerpt(false));
	}

	/**
	 * Retrieve a META description for a Post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return string
	 */	
	public function getMetaKeywords(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		if (Mage::helper('wordpress/plugin_allInOneSeo')->isEnabled()) {
			$keys = trim($this->getMetaValue($post, '_aioseop_keywords'));
		
			return $keys ? $keys : null;
		}
		
		return null;
	}
	
	/**
	 * Retrieve the page title for a Post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return string
	 */	
	public function getPageTitle(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		if (Mage::helper('wordpress/plugin_allInOneSeo')->isEnabled()) {
			$title = trim($this->getMetaValue($post, '_aioseop_title'));
		
			return $title ? $title : strip_tags($post->getPostTitle());
		}
		
		return strip_tags($post->getPostTitle());
	}

	/**
	 * Retrieve a META value for a Post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return string
	 */	
	public function getMetaValue(Fishpig_Wordpress_Model_Post_Abstract $post, $key, $limit = 1)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(Mage::helper('wordpress/db')->getTableName('postmeta'), 'meta_value')
			->where('post_id=?', $post->getId())
			->where('meta_key=?', $key)
			->limit($limit);

		if ($limit == '1') {
			return $this->_getReadAdapter()->fetchOne($select);
		}
		
		return $this->_getReadAdapter()->fetchCol($select);
	}
	/**
	 * Retrieve a collection of post comments
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Comment_Collection
	 */
	public function getPostComments(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		return Mage::getResourceModel('wordpress/post_comment_collection')
			->addPostIdFilter($post->getId())
			->addCommentApprovedFilter()
			->addOrderByDate();
	}
	
	/**
	 * Retrieve the featured image for the post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return Fishpig_Wordpress_Model_Image $image
	 */
	public function getFeaturedImage(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		if ($images = $post->getImages()) {
			$select = $this->_getReadAdapter()
				->select()
				->from(Mage::helper('wordpress/db')->getTableName('postmeta'), 'meta_value')
				->where('post_id=?', $post->getId())
				->where('meta_key=?', '_thumbnail_id')
				->limit(1);

			if ($featuredImageId = $this->_getReadAdapter()->fetchOne($select)) {
				return Mage::getModel('wordpress/image')->load($featuredImageId);
			}
		}
	}
	
	/**
	 * Post a comment
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @param string $name
	 * @param string $email
	 * @param string $url
	 * @param string $comment
	 * @return false|Fishpig_Wordpress_Model_Post_Comment
	 */
	public function postComment(Fishpig_Wordpress_Model_Post_Abstract $post, $name, $email, $url, $content)
	{
		$comment = Mage::getModel('wordpress/post_comment')
			->setPost($post);
			
		$comment->setCommentAuthor($name);
		$comment->setCommentAuthorEmail($email);
		$comment->setCommentAuthorUrl($url);
		$comment->setCommentContent($content);
		
		try {
			$comment->save();
			return $comment;
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
			Mage::logException($e);
			Mage::getSingleton('core/session')->addError(Mage::helper('wordpress')->__($e->getMessage()));
			return false;
		}
	}
}
