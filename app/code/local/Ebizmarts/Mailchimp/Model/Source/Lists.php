<?php

class Ebizmarts_Mailchimp_Model_Source_Lists {

	protected $_lists = array();
	protected $_options = array();
	protected $_dropdown = array();

	public function toOptionArray(){

		if(!count($this->_options)){

			$this->getMailChimpLists();

			if(count($this->_lists)){
				foreach($this->_lists as $list){
					$this->_options[$list['id']] = array('value'=>$list['id'],
									   		  			 'label'=>$list['name']);
				}
	            array_unshift($this->_options, array('value'=>'','label'=> ''));
			}
		}

		return $this->_options;
    }

	public function toOptionDropdown(){

		if(!count($this->_dropdown)){

			$this->getMailChimpLists();

			if(count($this->_lists)){
				foreach($this->_lists as $list){
					$this->_dropdown[$list['id']] = $list['name'];
				}
			}
		}

		return $this->_dropdown;
    }

    protected function getMailChimpLists(){

		if(!count($this->_lists)){
			$lists = Mage::getSingleton('mailchimp/mailchimp')->getLists();
			if (isset($lists['error'])){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mailchimp')->__('Something went wrong, internal message: %s',$lists['error']));
				return false;
			}
			$this->_lists = $lists['data'];
		}
		return $this->_lists;

    }

}