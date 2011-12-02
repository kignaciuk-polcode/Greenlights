<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_Associated_Products extends Fishpig_Wordpress_Block_Post_View_Abstract
{
	public function getProducts($attributes = '*')
	{
		$collection = Mage::helper('wordpress/catalog_product')
			->getAssociatedProducts($this->getPost());
			
		if ($collection) {
			$collection->addAttributeToSelect($attributes)->load();
		}
		
		return $collection ? $collection : array();
	}
}
