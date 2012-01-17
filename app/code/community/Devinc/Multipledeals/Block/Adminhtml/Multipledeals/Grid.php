<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  
  public function __construct()
  {
      parent::__construct();
      $this->setId('multipledealsGrid');
      $this->setDefaultSort('multipledeals_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('multipledeals/multipledeals')->getCollection()
					->setOrder('status', 'ASC')  
					->setOrder('type', 'ASC')
					->setOrder('multipledeals_id', 'DESC');   
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('multipledeals_id', array(
          'header'    => Mage::helper('multipledeals')->__('Queue ID'),
          'align'     =>'left',
          'width'     => '30px',
          'index'     => 'multipledeals_id',
      ));
	
	  $deal_collection = Mage::getModel('multipledeals/multipledeals')->getCollection()->setOrder('multipledeals_id', 'DESC');      
      $deals_sku = array();
	  
	  foreach ($deal_collection as $deal) {      
          $deals_sku[] = $deal->product_id;          
      }		
	  
	  $productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('name')
				->addAttributeToSelect('sku')
				->addAttributeToFilter('entity_id', array('in' => $deals_sku))
                ->load();
	
      $products = array();
      $products_sku = array();
            
	  $products[] = 'No product assigned';   
	  $products_sku[] = 'No product assigned';   
			
	  foreach ($productCollection as $product) {
          $products[$product->entity_id] = $product->name;          
          $products_sku[$product->entity_id] = $product->sku;          
      }		
		
	  $this->addColumn('product_name', array( 
          'header'    => Mage::helper('multipledeals')->__('Product name'),
          'align'     => 'left',
          'index'     => 'product_id',
          'type'      => 'options',
          'options'   => $products
      ));   
	  
		
	  $this->addColumn('product_sku', array( 
          'header'    => Mage::helper('multipledeals')->__('Product SKU'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'product_id',
          'type'      => 'options',
          'options'   => $products_sku
      ));   
	  
	  $this->addColumn('type', array(
          'header'    => Mage::helper('multipledeals')->__('Deal Type'),
          'align'     => 'left',
          'index'     => 'type',
          'width'     => '70px',
          'type'      => 'options',
          'options'   => array(
              4 => 'Future Deal',
              1 => 'Main Deal',
              2 => 'Side Deal',
              3 => 'Recent Deal',
          ),
      )); 
	  
      $store = $this->_getStore();	  
	  $this->addColumn('deal_price', array( 
          'header'    => Mage::helper('multipledeals')->__('Deal Price'),
          'align'     => 'left',
          'index'     => 'deal_price',
          'width'     => '50px',
          'currency_code' => $store->getBaseCurrency()->getCode(),
          'type'      => 'price',
      ));     
	  
	  $this->addColumn('date_time_from', array(
          'header'    => Mage::helper('multipledeals')->__('Date/Time From'),
          'align'     => 'left',
          'width'     => '135px',
          'type'      => 'datetime',
          'default'   => '--',
          'index'     => 'date_from',
          'renderer'  => 'multipledeals/adminhtml_multipledeals_grid_renderer_datetimefrom',
      ));	
	  
	  $this->addColumn('date_time_to', array(
          'header'    => Mage::helper('multipledeals')->__('Date/Time To'),
          'align'     => 'left',
          'width'     => '135px',
          'type'      => 'datetime',
          'default'   => '--',
          'index'     => 'date_to',
          'renderer'  => 'multipledeals/adminhtml_multipledeals_grid_renderer_datetimeto',
      ));	

      $this->addColumn('qty_sold', array(
          'header'    => Mage::helper('multipledeals')->__('Qty Sold'),
          'align'     =>'left',
          'width'     => '30px',
          'index'     => 'qty_sold',
      ));

      $this->addColumn('nr_views', array(
          'header'    => Mage::helper('multipledeals')->__('Nr. Views'),
          'align'     =>'left',
          'width'     => '30px',
          'index'     => 'nr_views',
      ));
	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('multipledeals')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Queued',
              2 => 'Disabled',
              3 => 'Running',
              4 => 'Ended',
          ),
          'renderer'  => 'multipledeals/adminhtml_multipledeals_grid_renderer_status',
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('multipledeals')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('multipledeals')->__('Set as main deal'),
                        'url'       => array('base'=> '*/*/main'),
                        'field'     => 'id',
						'condition' => array('data' => 'type', 'operator' => 'eq', 'value' => '2')
                    ),		
                    array(
                        'caption'   => Mage::helper('multipledeals')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),		
                    array(
                        'caption'   => Mage::helper('multipledeals')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
						'confirm'  => Mage::helper('multipledeals')->__('Are you sure you want to delete this deal?')
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('multipledeals')->__('CSV'));
	  
      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
        $this->setMassactionIdField('multipledeals_id');
        $this->getMassactionBlock()->setFormFieldName('multipledeals');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('multipledeals')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('multipledeals')->__('Are you sure you want to delete these deals?')
        ));

        $statuses = Mage::getSingleton('multipledeals/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('multipledeals')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('multipledeals')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
  }

  protected function _getStore()
  {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
  }
 
  public function getRowNumber($id)
  {
	$visibility = array(2, 4);
	$collection = Mage::getModel('catalog/product')->getCollection()->setOrder('entity_id', 'DESC')->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter('entity_id', array('gteq' => $id));
	
	return count($collection);
  }

  public function getRowUrl($row)
  {
	  $row_num = $this->getRowNumber($row->getProductId());
	  $page = ceil($row_num/20);
	  
      return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'page' => $page));
  }

}