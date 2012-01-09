<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post extends Fishpig_Wordpress_Model_Mysql4_Post_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post', 'ID');
	}

	/**
	 * Retrieve a collection of post tags
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Tag_Collection
	 */
	public function getPostTags(Fishpig_Wordpress_Model_Post $post)
	{
		return Mage::getResourceModel('wordpress/post_tag_collection')
					->addPostIdFilter($post->getId());
	}
	
	/**
	 * Retrieve a collection of categories
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @retrun Fishpig_Wordpress_Model_Post_Category_Collection
	 */
	public function getParentCategories(Fishpig_Wordpress_Model_Post $post)
	{
		return Mage::getResourceModel('wordpress/post_category_collection')
			->addPostIdFilter($post->getId());
	}

	/**
	 * Retrieve the position of a post in a product
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @param int $productId
	 * @return int
	 */
	public function getPositionInProduct(Fishpig_Wordpress_Model_Post $post, $productId)
	{
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$select = $read->select()
			->from(Mage::getSingleton('core/resource')->getTableName('wordpress_product_post'), 'position')
			->where('post_id=?', $post->getId())
			->where('product_id=?', $productId)
			->limit(1);
			
		return number_format($read->fetchOne($select), 0);
	}
}
