<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @copyright  Copyright (c) 2011 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Netzarbeiter_GroupsCatalog_Block_Adminhtml_Customer_Edit_Tab_Visibleproducts extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_visibleProductIds;

	protected $_customerGroup;

	public function __construct()
	{
		parent::__construct();
		$this->setId('customer_visible_products');
		$this->setDefaultSort('entity_id');
		$this->setUseAjax(true);
		$this->setGridHeader($this->__('Products visible to the customer group %s', $this->getCustomerGroupName()));
        $this->setRowClickCallback('visibleProducts.onRowClick.bind(visibleProducts)');
        $this->setRowInitCallback('visibleProducts.onRowInit.bind(visibleProducts)');
		//$this->setCheckboxCheckCallback('visibleProducts.onBoxCheck.bind(visibleProducts)'.productGridCheckboxCheck.bind(bSelection)');
		//$this->setTemplate('netzarbeiter/groupscatalog/customer/form.phtml');
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for is visible flag
		if ($column->getId() == 'is_visible') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
			}
			elseif(!empty($productIds)) {
				$this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	public function getCustomer()
	{
		return Mage::registry('current_customer');
	}
	
	public function getCustomerGroup()
	{
		if (! isset($this->_customerGroup))
		{
			$this->_customerGroup = Mage::getModel('customer/group')
				->load($this->getCustomer()->getGroupId());
		}
		return $this->_customerGroup;
	}

	public function getCustomerGroupName()
	{
		return $this->getCustomerGroup()->getCode();
	}

	protected function _getVisibleProductIds()
	{
		if (! isset($this->_visibleProductIds))
		{
			$collection = Mage::getModel('catalog/product')->getCollection()
				->addStoreFilter($this->getRequest()->getParam('store'))
			;
			Mage::helper('groupscatalog')->addGroupsFilterToProductCollection($collection, $this->getCustomer()->getGroupId());
			$this->_visibleProductIds = array();
			foreach ($collection as $product) $this->_visibleProductIds[] = $product->getId();
		}
		return $this->_visibleProductIds;

	}

	protected function _prepareCollection()
	{
		if ($this->getCustomer()->getId() && count($this->_getVisibleProductIds())) {
			$this->setDefaultFilter(array('is_visible' => 1));
		}
		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect(array('name', 'sku', 'price'))
			->addStoreFilter($this->getRequest()->getParam('store'))
		;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('is_visible', array(
				'header_css_class' => 'a-center',
				'type'       => 'checkbox',
				'name'       => 'is_visible',
				'values'     => $this->_getSelectedProducts(),
				'align'      => 'center',
				'index'      => 'entity_id',
				'inline_css' => 'checkbox netzarbeiter-visible-products',
				'renderer'   => 'groupscatalog/adminhtml_widget_grid_column_renderer_visible',
				//'row_clicked_callback' => 'visibleProducts.rowClick',
				//'checkbox_check_callback' => 'visibleProducts.boxCheck',
			));
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('catalog')->__('ID'),
			'sortable'  => true,
			'width'     => '60',
			'index'     => 'entity_id'
			));
		$this->addColumn('name', array(
			'header'    => Mage::helper('catalog')->__('Name'),
			'index'     => 'name'
			));
		$this->addColumn('sku', array(
			'header'    => Mage::helper('catalog')->__('SKU'),
			'width'     => '80',
			'index'     => 'sku'
			));
		$this->addColumn('price', array(
			'header'    => Mage::helper('catalog')->__('Price'),
			'type'  => 'currency',
			'width'     => '1',
			'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
			'index'     => 'price'
			));

		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/visibleProductsGrid', array('_current' => true));
	}

	protected function _getSelectedProducts()
	{
		$products = $this->getRequest()->getPost('selected_products');
		if (is_null($products)) {
			/*
			 * Return array with visible product ids
			 */
			return $this->_getVisibleProductIds();
		}
		return $products;
	}
}