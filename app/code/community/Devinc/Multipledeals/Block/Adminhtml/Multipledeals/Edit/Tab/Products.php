<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');		
		 
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
			
		$visibility = array(2, 4);
        $collection->addAttributeToFilter('visibility', $visibility);

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
		
        return $this;
    }

    protected function _prepareColumns()
    {
		$this->addColumn('product_select', array(
                'header_css_class'  => 'a-center',
                'type'              => 'radio',
                'name'              => 'product_select',
                'align'             => 'center',
				'class'				=> 'required-entry',
                'index'             => 'entity_id',
                'values'            => array(0 => 163),
				'renderer'  		=> 'multipledeals/adminhtml_multipledeals_edit_renderer_radio',
        ));
		
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
		
		/* $this->addColumn('thumbnail',
                array(
                    'header'=> Mage::helper('catalog')->__('Thumbnail'),
					'width' => '90px',
                    'type'  => 'image',
                    'index' => 'entity_id',
					'renderer'  => 'multipledeals/adminhtml_multipledeals_edit_renderer_image',
        ));
			 */
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '150px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
       
		/* $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        )); */
		
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type'  => 'number',
                'index' => 'qty',
        ));        

        $this->addColumn('product_status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
		
		$this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'adminhtml/catalog_product/edit/',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id',
                        'target'   => 'blank'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
        

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        return parent::_prepareColumns();
    }
	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/products', array('_current'=>true));
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit/', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    

}