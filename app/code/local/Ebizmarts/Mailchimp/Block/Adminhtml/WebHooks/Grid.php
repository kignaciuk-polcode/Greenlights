<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_WebHooks_Grid extends Mage_Adminhtml_Block_Widget_Grid{

     public function __construct(){

     	parent::__construct();
        $this->setId('webHooksGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
		$this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){

		Mage::getSingleton('mailchimp/webHooks')->updateController();
		$collection = Mage::getSingleton('mailchimp/mysql4_helper_collection');
        $lists = Mage::getSingleton('mailchimp/source_lists')->toOptionArray();
        if(is_array($lists) && count($lists)){
        	foreach($lists as $list){
        		if($list['value']){
	        		$id = $list['value'];
		        	$return = Mage::getSingleton('mailchimp/webHooks')->mainWebHooksAction($id);
					$item = new Varien_Object;
		          	$item->setListId($id)
		          		 ->setName($list['label'])
		          		 ->setSubscribe((bool)$return['actions']['subscribe'])
		          		 ->setUnsubscribe((bool)$return['actions']['unsubscribe'])
		          		 ->setProfile((bool)$return['actions']['profile'])
		          		 ->setCleaned((bool)$return['actions']['cleaned'])
		          		 ->setUpemail((bool)$return['actions']['upemail'])
		          		 ->setUser((bool)$return['sources']['user'])
		          		 ->setAdmin((bool)$return['sources']['admin'])
		          		 ->setApi((bool)$return['sources']['api']);
		          	$collection->addItem($item);
	          	}
			}
        }

        $this->setCollection($collection);
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

	protected function _prepareColumns() {

		$helper = Mage::helper('mailchimp');

        $this->addColumn('list_id', array(
        	'header'    => $helper->__('List Id'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'list_id'
		));

		$this->addColumn('name', array(
        	'header'    => $helper->__('List Name'),
           	'align'     =>'left',
           	'width'     => '300px',
           	'index'     => 'name'
       	));

        $this->addColumn('subscribe', array(
        	'header'    => $helper->__('Actions: Subscribe'),
            'field_name'=> 'subscribe',
            'align'     => 'center',
            'index'     => 'subscribe',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
		));

		$this->addColumn('unsubscribe', array(
			'header'    => $helper->__('Actions: Unsubscribe'),
            'align'     => 'center',
            'field_name'=> 'unsubscribe',
            'index'     => 'unsubscribe',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
       	));

        $this->addColumn('profile', array(
        	'header'    => $helper->__('Actions: Update Profile'),
            'field_name'=> 'profile',
            'align'     => 'center',
            'index'     => 'profile',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
      	));

		$this->addColumn('cleaned', array(
        	'header'    => $helper->__('Actions: Cleaned'),
            'field_name'=> 'cleaned',
            'align'     => 'center',
            'index'     => 'cleaned',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
      	));

        $this->addColumn('upemail', array(
        	'header'    => $helper->__('Actions: Update Email'),
            'field_name'=> 'upemail',
            'align'     => 'center',
            'index'     => 'upemail',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
      	));

        $this->addColumn('user', array(
        	'header'    => $helper->__('Source: User'),
            'field_name'=> 'user',
            'align'     => 'center',
            'index'     => 'user',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
      	));

		$this->addColumn('admin', array(
        	'header'    => $helper->__('Source: Admin'),
            'field_name'=> 'admin',
            'align'     => 'center',
            'index'     => 'admin',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
		));

        $this->addColumn('api', array(
        	'header'    => $helper->__('Source: API'),
            'field_name'=> 'api',
            'align'     => 'center',
            'index'     => 'api',
			'type'      => 'checkbox',
          	'sortable'  => false,
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
      	));

        return parent::_prepareColumns();
		}
}