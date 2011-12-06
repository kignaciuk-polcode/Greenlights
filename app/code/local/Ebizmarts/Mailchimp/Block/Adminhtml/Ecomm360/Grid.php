<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ecomm360_Grid extends Mage_Adminhtml_Block_Widget_Grid{

      public function __construct(){

          parent::__construct();
          $this->setId('ecomm360Grid');
          $this->setDefaultSort('ecomm_id');
          $this->setDefaultDir('DESC');
          $this->setSaveParametersInSession(true);
      }

      protected function _prepareCollection(){

          $collection = Mage::getModel('mailchimp/ecomm360')->getCollection();
          $this->setCollection($collection);
           return parent::_prepareCollection();
       }

       protected function _prepareColumns(){

           $this->addColumn('ecomm_id', array(
               'header'    => Mage::helper('mailchimp')->__('Id #'),
               'align'     =>'left',
               'width'	   =>'20px',
               'index'     => 'ecomm_id'
           ));

           $this->addColumn('store_id', array(
               'header'    => Mage::helper('mailchimp')->__('Store #'),
               'align'     =>'left',
               'width'	   =>'20px',
               'index'     => 'store_id'
           ));

           $this->addColumn('increment_id', array(
               'header'    => Mage::helper('mailchimp')->__('Order #'),
               'align'     => 'left',
               'width'	   =>'20px',
               'index'     => 'increment_id'
           ));

           $this->addColumn('campaign_id', array(
               'header'    => Mage::helper('mailchimp')->__('Campaign Id'),
               'align'     => 'left',
               'index'     => 'campaign_id'
           ));

           $this->addColumn('sent_time', array(
              'header'    => Mage::helper('mailchimp')->__('Sent Time'),
              'align'     => 'left',
              'width'     => '180px',
              'type'      => 'datetime',
              'default'   => '--',
              'index'     => 'sent_time',
          ));

          return parent::_prepareColumns();
      }

      public function getRowUrl($row){

		if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
  			return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getOrderId()));
		}
		return false;
      }
  }