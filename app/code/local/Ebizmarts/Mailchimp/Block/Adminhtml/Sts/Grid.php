<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Sts_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	public function __construct(){

		parent::__construct();
        $this->setId('stsGrid');
		$this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection(){

        $collection = Mage::getSingleton('mailchimp/mysql4_sts');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){

        $this->addColumn('emailadress', array(
            'header'=> Mage::helper('mailchimp')->__('Email Adress'),
            'type'  => 'text',
            'index' => 'emailadress',
            'filter' =>false
        ));
        $this->addColumn('remove',
            array(
                'header'    =>  Mage::helper('mailchimp')->__('Remove'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getEmailadress',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mailchimp')->__('Remove'),
                        'url'       => array('base'=> '*/*/remove'),
                        'field'     => 'emailadress'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'emailadress',
                'is_system' => true,
        ));
        $this->addColumn('testemail',
            array(
                'header'    =>  Mage::helper('mailchimp')->__('Send Test Email'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getEmailadress',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mailchimp')->__('Send Test Email'),
                        'url'       => array('base'=> '*/*/sendtest'),
                        'field'     => 'emailadress'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'emailadress',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction(){

        $this->setMassactionIdField('emailadress');
        $this->getMassactionBlock()->setFormFieldName('emailadress');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('remove_email', array(
             'label'=> Mage::helper('sales')->__('Remove'),
             'url'  => $this->getUrl('*/*/massremove'),
        ));
        return $this;
    }

}