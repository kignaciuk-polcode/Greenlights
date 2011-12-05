<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Display the associated posts grid
	 *
	 */
	public function postAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function postGridAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function categoryAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function categoryGridAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	
	/**
	 * Initialise the product model
	 * This should only be called via AJAX actions
	 *
	 * @return false|Mage_Catalog_Model_Product
	 */
	protected function _initProduct()
	{
		if (!Mage::registry('product')) {
			if ($productId = $this->getRequest()->getParam('id')) {
				$product = Mage::getModel('catalog/product')->load($productId);
				
				if ($product->getId()) {
					Mage::register('product', $product);
					return $product;
				}
			}
		}
		
		return false;
	}
}
