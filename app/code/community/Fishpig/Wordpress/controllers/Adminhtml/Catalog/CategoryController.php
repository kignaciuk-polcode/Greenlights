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
		if (!$this->_isSingleStoreMode() && !Mage::app()->getRequest()->getParam('store')) {
			$this->_forward('storeSelector');
		}
		else {
			if ($this->_initWordPressDatabaseForStore()) {
				$this->_initCategory();
				$this->loadLayout();
				$this->renderLayout();
			}
			else {
				$this->_forward('noWordPressDatabase');
			}
		}
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function postGridAction()
	{
		if (!$this->_isSingleStoreMode() && !Mage::app()->getRequest()->getParam('store')) {
			$this->_forward('storeSelector');
		}
		else {
			if ($this->_initWordPressDatabaseForStore()) {
				$this->_initCategory();
				$this->loadLayout();
				$this->renderLayout();
			}
			else {
				$this->_forward('noWordPressDatabase');
			}
		}
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function categoryAction()
	{
		if (!$this->_isSingleStoreMode() && !Mage::app()->getRequest()->getParam('store')) {
			$this->_forward('storeSelector');
		}
		else {
			if ($this->_initWordPressDatabaseForStore()) {
				$this->_initCategory();
				$this->loadLayout();
				$this->renderLayout();
			}
			else {
				$this->_forward('noWordPressDatabase');
			}
		}
	}
	
	/**
	 * Display the associated posts grid
	 *
	 */
	public function categoryGridAction()
	{
		if (!$this->_isSingleStoreMode() && !Mage::app()->getRequest()->getParam('store')) {
			$this->_forward('storeSelector');
		}
		else {
			if ($this->_initWordPressDatabaseForStore()) {
				$this->_initCategory();
				$this->loadLayout();
				$this->renderLayout();
			}
			else {
				$this->_forward('noWordPressDatabase');
			}
		}
	}
	
	public function noWordPressDatabaseAction()
	{
		$this->getResponse()->setBody('<p style="font-size: 18px; margin-top: 40px; text-align: center;">There was an error connecting to the WordPress database for this store.</p>');
	}
	
	protected function _initWordPressDatabaseForStore()
	{
		if ($this->_isSingleStoreMode()) {
			if ($store = Mage::helper('wordpress')->getCurrentFrontendStore()) {
				if ($store->getId()) {
					$website = $store->getWebsite();
					
					if ($website->getId()) {
						$this->getRequest()->setParam('store', $store->getId());
						return Mage::helper('wordpress/db')->connect($website->getCode(), $store->getCode());
					}
				}
			}
		}
		else {
			if ($storeId = Mage::app()->getRequest()->getParam('store', false)) {
				$store = Mage::getModel('core/store')->load($storeId);
				
				if ($store->getId()) {
					$website = $store->getWebsite();
					
					if ($website->getId()) {
						return Mage::helper('wordpress/db')->connect($website->getCode(), $store->getCode());
					}
				}
			}
		}	
	
		return false;
	}

	public function storeSelectorAction()
	{
		$this->getResponse()->setBody('<p style="font-size: 18px; margin-top: 40px; text-align: center;">You must select a store using the store changer (top left) before associating blog items with this product.</p>');

	}
	
	/**
	 * Determine whether only 1 store exists
	 *
	 * @return bool
	 */
	protected function _isSingleStoreMode()
	{
		return Mage::app()->isSingleStoreMode();
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
