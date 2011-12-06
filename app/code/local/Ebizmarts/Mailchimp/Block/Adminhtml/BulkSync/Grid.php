  <?php

class Ebizmarts_Mailchimp_Block_Adminhtml_BulkSync_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	protected $_bulkactionBlockName = 'mailchimp/adminhtml_widget_grid_bulkaction';

	public function __construct(){

    	parent::__construct();
        $this->setId('bulkSyncGrid');
        $this->setDefaultSort('created_time', 'desc');
        $this->setSaveParametersInSession(true);
	}

    public function getBulkBlockName(){

        return $this->_bulkactionBlockName;
    }

    protected function _prepareMassactionBlock(){

        $this->setChild('massaction', $this->getLayout()->createBlock($this->getBulkBlockName()));
        $this->_prepareMassaction();
        if($this->getMassactionBlock()->isAvailable()) {
            $this->_prepareMassactionColumn();
        }
        return $this;
    }

 	protected function _prepareMassaction(){

        $this->setMassactionIdField('name');
        $this->getMassactionBlock()->setFormFieldName('bulkSyncGrid');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $helper = Mage::helper('mailchimp');
        $stores = Mage::getModel('mailchimp/source_cms')->toOptionArray();

        $this->getMassactionBlock()->addItem(Ebizmarts_Mailchimp_Model_BulkSynchro::WAY_IMPORT, array(
             'label'    => $helper->__('Import List'),
             'complete' => $helper->__('A new list have been created.'),
             'additional'   => array(
                'list'    => array(
                     'name'     => 'list',
                     'type'     => 'select',
                     'class'	=> 'selectChimp',
                     'label'    => $helper->__('From list'),
                     'values'   => Mage::getSingleton('mailchimp/source_lists')->toOptionArray()
                 ),
                 'store'    => array(
                     'name'     => 'import',
                     'type'     => 'select',
                     'class'	=> 'selectChimp',
                     'label'    => $helper->__('To store'),
                     'values'   => $stores
                 ),
	             'start'  => array(
	                     'name'     => 'start',
	                     'type'     => 'text',
	                     'class'	=> 'textChimp',
	                     'label'    => $helper->__('Starts in Page'),
	                     'value'  => '0'
	                 ),
	             'limit'  => array(
	                     'name'     => 'limit',
	                     'type'     => 'text',
	                     'class'	=> 'textChimp',
	                     'label'    => $helper->__('Subscribers limit'),
	                     'value'    => '15000'
	                 )
            )
        ));

        $this->getMassactionBlock()->addItem(Ebizmarts_Mailchimp_Model_BulkSynchro::WAY_EXPORT, array(
             'label'    => $helper->__('Export List'),
             'additional'   => array(
	             'store'    => array(
	                 'name'     => 'store',
	                 'type'     => 'select',
	                 'class'	=> 'selectChimp',
	                 'label'    => $helper->__('From store'),
	                 'values'   => $stores
	             ),
				'list'    => array(
				     'name'     => 'list',
				     'type'     => 'select',
				     'class'	=> 'selectChimp',
				     'label'    => $helper->__('To list'),
				     'values'   => Mage::getSingleton('mailchimp/source_lists')->toOptionArray()
				 )
            )
        ));

        return $this;
    }

	 protected function _prepareCollection(){

        $collection = Mage::getSingleton('mailchimp/mysql4_bulk_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns(){

    	$helper = Mage::helper('mailchimp');

        $this->addColumn('created_time', array(
            'header'    => $helper->__('Created Time'),
            'index'     => 'created_object',
            'type'      => 'datetime',
            'width'     => '250px'
        ));

        $this->addColumn('updated_time', array(
            'header'    => $helper->__('Updated Time'),
            'index'     => 'updated_object',
            'type'      => 'datetime',
            'width'     => '250px'
        ));

        $this->addColumn('store', array(
            'header'    => $helper->__('Store'),
            'index'     => 'store',
			'type'      => 'options',
            'options'   => Mage::getSingleton('mailchimp/source_cms')->getStores(),
            'index'     =>'store',
            'width'     => '150px'
        ));

        $this->addColumn('type', array(
            'header'    => $helper->__('Type'),
            'type'      => 'options',
            'options'   => Mage::getSingleton('mailchimp/source_bulkType')->toOptionArray(),
            'index'     =>'type',
            'width'     => '150px'
        ));

		$this->addColumn('list_id', array(
              'header'    => $helper->__('List'),
              'width'     => '250px',
              'align'     => 'center',
              'type'      => 'options',
              'options'   => Mage::getSingleton('mailchimp/source_lists')->toOptionDropdown(),
              'index'     => 'list'
        ));

		$this->addColumn('size', array(
            'header'    => $helper->__('Size zipped, in Bytes'),
            'index'     => 'size',
            'type'      => 'number',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('action', array(
            'header'    => $helper->__('Action'),
            'type'      => 'action',
            'width'     => '80px',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
            	array(
	                'url'       => $this->getUrl('*/*/delete', array('time' => '$created_time', 'type' => '$type', 'list' => '$list', 'store' => '$store')),
	                'caption'   => $helper->__('Delete'),
	                'confirm'   => $helper->__('Are you sure you want to DELETE this?')),
				array(
	                'url'       => $this->getUrl('*/*/run', array('time' => '$created_time', 'type' => '$type', 'list' => '$list', 'store' => '$store')),
	                'caption'   => $helper->__('Run'),
	                'confirm'   => $helper->__('Are you sure you want to RUN this?')),
				array(
	                'url'       => $this->getUrl('*/*/download', array('time' => '$created_time', 'type' => '$type', 'list' => '$list', 'store' => '$store')),
	                'caption'   => $helper->__('Download'),
	                'confirm'   => $helper->__('Are you sure you want to DOWNLOAD this?'))
                ),
            'index'     => 'type',
            'sortable'  => false
        ));

        return $this;
    }

 }