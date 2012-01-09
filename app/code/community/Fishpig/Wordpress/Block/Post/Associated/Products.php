<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_Associated_Products extends Mage_Catalog_Block_Product_Abstract
{
	/**
	 * Retrieve a collection of products
	 *
	 * @return array|Mage_Catalog_Model_Mysql4_Resource_Eav_Mysql4_Product_Collection
	 */
	public function getProducts($attributes = array('name', 'price', 'product_url', 'thumbnail'))
	{
		$collection = Mage::helper('wordpress/catalog_product')->getAssociatedProducts($this->getPost());
			
		return $collection ? $collection->addAttributeToSelect($attributes)->load() : array();
	}
	
	/**
	 * Retrieve the post object
	 *
	 * @return false|Fishpig_Wordpress_Model_Post
	 */
	public function getPost()
	{
		return Mage::registry('wordpress_post');
	}
}
