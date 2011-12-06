<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ctemplate_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	public function __construct(){

		parent::__construct();
        $this->setId('ctemplateGrid');
        $this->setDefaultSort('tid');
        $this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection(){

        $this->setCollection(Mage::helper('mailchimp')->getCtemplatesCollection());
		if ($this->getCollection()) {

        	$this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $column = $this->_columns[$columnId]->getFilterIndex() ?
                    $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
                $this->setCollection($this->getCollection()->sortCollection($column, $dir));
            }

            $this->getCollection()->load();
            $this->_afterLoadCollection();
		}

        return $this;
    }

    protected function _prepareColumns(){

		$helper = Mage::helper('mailchimp');

		$this->addColumn('id', array(
       		'header'    => $helper->__('Template Id'),
		   	'align'     =>'left',
		   	'width'     => '80px',
		   	'index'     => 'id'
   		));
		$this->addColumn('name', array(
       		'header'    => $helper->__('Template Name'),
		   	'align'     =>'left',
		   	'index'     => 'name'
   		));
		$this->addColumn('layout', array(
       		'header'    => $helper->__('Layout'),
            'align'     => 'center',
            'width'     => '200px',
		   	'index'     => 'layout'
   		));
		$this->addColumn('tid', array(
       		'header'    => $helper->__('Type'),
            'align'     => 'center',
            'width'     => '150px',
		   	'index'     => 'tid'
   		));
		$this->addColumn('date_created', array(
			'header'    => $helper->__('Date Created'),
          	'align'     => 'left',
          	'width'     => '180px',
          	'type'      => 'datetime',
          	'default'   => '--',
          	'index'     => 'date_created'
        ));
		$this->addColumn('active', array(
        	'header'    => $helper->__('Is Active'),
            'align'     => 'center',
            'width'     => '20px',
            'index'     => 'active',
            'type'      => 'options',
            'options'   => array(
            	'Y' => $helper->__('Yes'),
                'N' => $helper->__('No'),
          	)
      	));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction(){

		$helper = Mage::helper('mailchimp');
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ctemplates');
        $this->getMassactionBlock()->setUseSelectAll(true);

        $this->getMassactionBlock()->addItem('deactivate', array(
             'label'=> $helper->__('Deactivate'),
             'url'  => $this->getUrl('*/*/massDeactivate'),
             'confirm' => $helper->__('Are you sure you want to DEACTIVATE all the selected campaign templates?')
        ));
        $this->getMassactionBlock()->addItem('reactivate', array(
             'label'=> $helper->__('Reactivate'),
             'url'  => $this->getUrl('*/*/massReactivate'),
             'confirm' => $helper->__('Are you sure you want to REACTIVATE all the selected campaign templates?')
        ));
        return $this;
    }

    public function getRowUrl($row){

  		return $this->getUrl('*/*/edit', array('id' =>$row->getId(),'tid' =>urlencode($row->getTid())));
	}

}