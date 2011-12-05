<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Display the associated posts grid
	 *
	 */
	public function postAction()
	{
		$this->_initCategory();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function postGridAction()
	{
		$this->_initCategory();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function categoryAction()
	{
		$this->_initCategory();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function categoryGridAction()
	{
		$this->_initCategory();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Initialise the category model
	 * This should only be called via AJAX actions
	 *
	 * @return false|Mage_Catalog_Model_Category
	 */
	protected function _initCategory()
	{
		if (!Mage::registry('category')) {
			if ($categoryId = $this->getRequest()->getParam('id')) {
				$category = Mage::getModel('catalog/category')->load($categoryId);
				
				if ($category->getId()) {
					Mage::register('category', $category);
					return $category;
				}
			}
		}
		
		return false;
	}
}
