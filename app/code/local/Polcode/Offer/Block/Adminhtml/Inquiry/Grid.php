    <?php
     
    class Polcode_Offer_Block_Adminhtml_Inquiry_Grid extends Mage_Adminhtml_Block_Widget_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('inquiryGrid');
            // This is the primary key of the database
            $this->setDefaultSort('inquiry_id');
            $this->setDefaultDir('ASC');
            $this->setSaveParametersInSession(true);
        }
     
        protected function _prepareCollection()
        {
            $collection = Mage::getModel('offer/inquiry')->getCollection();
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
     
        protected function _prepareColumns()
        {
            $this->addColumn('inquiry_id', array(
                'header'    => Mage::helper('offer/inquiry')->__('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'inquiry_id',
            ));
     
            $this->addColumn('customer_id', array(
                'header'    => Mage::helper('offer/inquiry')->__('Customer ID'),
                'align'     =>'right',
                'index'     => 'customer_id',
            ));
     
            
            $this->addColumn('submitted', array(
                'header'    => Mage::helper('offer/inquiry')->__('Submitted'),
                'align'     =>'center',
                'index'     => 'submitted',
            ));
            
     
            $this->addColumn('updated_at', array(
                'header'    => Mage::helper('offer/inquiry')->__('Update Time'),
                'align'     => 'left',
                'width'     => '120px',
                'type'      => 'date',
                'default'   => '--',
                'index'     => 'updated_at',
            ));
     
//            $this->addColumn('update_time', array(
//                'header'    => Mage::helper('<module>')->__('Update Time'),
//                'align'     => 'left',
//                'width'     => '120px',
//                'type'      => 'date',
//                'default'   => '--',
//                'index'     => 'update_time',
//            ));   
     
     
//            $this->addColumn('status', array(
//     
//                'header'    => Mage::helper('<module>')->__('Status'),
//                'align'     => 'left',
//                'width'     => '80px',
//                'index'     => 'status',
//                'type'      => 'options',
//                'options'   => array(
//                    1 => 'Active',
//                    0 => 'Inactive',
//                ),
//            ));
     
            return parent::_prepareColumns();
        }
     
        public function getRowUrl($row)
        {
            return $this->getUrl('*/*/view', array('id' => $row->getId()));
        }
     
     
    }