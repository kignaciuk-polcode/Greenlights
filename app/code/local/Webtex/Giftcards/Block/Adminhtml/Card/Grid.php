<?php
class Webtex_Giftcards_Block_Adminhtml_Card_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardsGrid');
        $this->setDefaultSort('card_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('giftcards/card')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $hlp = Mage::helper('giftcards');

        $this->addColumn('card_id', array(
            'header'    => $hlp->__('Card ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'card_id',
            'type'      => 'number',
        ));

        $this->addColumn('card_code', array(
            'header'    => $hlp->__('Card Code'),
            'align'     => 'left',
            'index'     => 'card_code',
        ));

        $this->addColumn('initial_value', array(
            'header'    => $hlp->__('Card Value'),
            'align'     => 'right',
            'index'     => 'initial_value',
            'type'      => 'currency',
            'currency'  => 'currency_code',
        ));

        $this->addColumn('order_link',
            array(
                'header'    =>  Mage::helper('customer')->__('Order'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getOrderId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('View'),
                        'url'     => array('base'=>'adminhtml/sales_order/view'),
                        'field'   => 'order_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'frame_callback' => array('Webtex_Giftcards_Block_Adminhtml_Card_Grid', 'getOrderLink'),
        ));

        $this->addColumn('date_created', array(
            'header'    => $hlp->__('Date Created'),
            'align'     => 'left',
            'index'     => 'date_created',
            'type'      => 'datetime',
            'width'     => '160px',
        ));

        $this->addColumn('status', array(
            'header'    => $hlp->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'filter_index' => 'main_table.status',
            'options'   => array(
                'A' => $hlp->__('Active'),
                'I' => $hlp->__('Inactive'),
            ),
        ));

        $this->addColumn('gift_card_type', array(
            'header'    => $hlp->__('Card Type'),
            'index'     => 'gift_card_type',
            'type'      => 'options',
            'filter_index' => 'main_table.gift_card_type',
            'options'   => array(
                'P' => $hlp->__('Print'),
                'E' => $hlp->__('E-mail'),
            ),
        ));
        
        return parent::_prepareColumns();
    }
    
    
    public function getOrderLink($renderedValue, $row, $column, $flag) {
      $order = Mage::getModel('sales/order')->loadByAttribute('entity_id', $row->getOrderId());
      return str_replace('View', $order->getIncrementId(), $renderedValue);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $block = $this->getMassactionBlock();

        $block->setFormFieldName('card');

        $block->addItem('delete', array(
             'label'=> Mage::helper('giftcards')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('giftcards')->__('Are you sure?')
        ));

        return $this;
    }
}
