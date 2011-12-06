<?php

class Ebizmarts_Mailchimp_Block_Customer_Newsletter extends Mage_Customer_Block_Newsletter {

    protected $_lists;
    protected $_listsFullData;
    protected $_groups;
    protected $_additionalSubscribedlists;
    protected $_additionalLists;

    const CUSTOMER_CREATE_URL    = '/customer/account/create/';

    public function isMailChimpEnabled($store){

		$store = ($store)? $store : Mage::app()->getStore()->getStoreId() ;
		return Mage::helper('mailchimp')->mailChimpEnabled($store);
    }

	public function canShowBigBox(){

		$store = Mage::app()->getStore()->getStoreId();

		if(Mage::helper('mailchimp')->getGeneralConfig('listid',$store)){
				return true;
		}
        return false;
    }

    public function getLists(){

		$store = Mage::app()->getStore()->getStoreId();
        $this->_loadLists($store);
        return $this->_lists;
    }

	protected function _loadLists($store){

        if (empty($this->_lists)) {

            $this->_lists = array();

			$lists = Mage::helper('mailchimp')->getAvailablelists($store);

        } else {
            return;
        }

        if( isset($lists) && count($lists) == 0 ) return;

		foreach($lists as $k=>$v){
			if($v != null){
				$this->_lists[$k] = $v;
				unset($lists[$k]);
			}
		}

        ksort($this->_lists);
    }


	protected function _loadListsFullData(){

		 if (empty($this->_listsFullData)) {

            $this->_listsFullData = array();

			$lists = Mage::getSingleton('mailchimp/mailchimp')->getLists();

        } else {
            return;
        }

        if( isset($lists) && $lists['total'] == 0 ) return;

		foreach($lists['data'] as $k=>$v){
			if($v != null){
				$this->_listsFullData[$v['id']] = $v;
				unset($lists[$k]);
			}
		}

        ksort($this->_listsFullData);
	}

    public function getSubscribedAdditionalLists(){

		$this->getLists();
        $this->_loadAdditionalSubscribedLists();
        return $this->_additionalSubscribedlists;
    }

    public function _loadAdditionalSubscribedLists(){

        if (empty($this->_additionalSubscribedlists)) {

            $this->_additionalSubscribedlists = array();

			$lists = $this->_lists;

        } else {
            return;
        }

        if( isset($lists) && count($lists) == 0 ) return;

		$col = Mage::getSingleton('mailchimp/subscripter')->getCollection()
			->addFieldToFilter('store_id', Mage::app()->getStore()->getStoreId())
			->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
			->addFieldToFilter('is_subscribed', true);

        foreach ($col as $item) {
        	if(array_key_exists($item->getListId(),$lists)){
        		$item->setName($lists[$item->getListId()]);
        		$this->_additionalSubscribedlists[$item->getListId()] = $item;
        		unset($lists[$item->getListId()]);
        	}
        }
        ksort($this->_additionalSubscribedlists);
    }

	public function getAdditionalLists($store){

		$store = ($store)? $store : Mage::app()->getStore()->getStoreId() ;
		if(Mage::helper('mailchimp')->getAvailableLists($store)){
			$this->getLists();
	        $this->getSubscribedAdditionalLists();
	        $this->_loadAdditionalLists();
		}
        return $this->_additionalLists;
    }

    public function _loadAdditionalLists(){

		if (empty($this->_additionalLists)) {
            $this->_additionalLists = array();

			$listsAll = $this->_lists;
			$listsSubscribed = $this->_additionalSubscribedlists;

        } else {
            return;
        }

        if( isset($listsAll) && count($listsAll) == 0 ) return;

        foreach ($listsSubscribed as $subscribed) {
        	if(array_key_exists($subscribed->getListId(),$listsAll)){
        		$this->_additionalLists[$subscribed->getListId()] = $subscribed;
        		unset($listsAll[$subscribed->getListId()]);
        	}
        }
        if(count($listsAll)){
        	foreach($listsAll as $k=>$v){
        		$item = new Varien_Object;
        		$item->setName($v)
        			 ->setListId($k)
    				 ->setIsSubscribed(false);
				$this->_additionalLists[$k] = $item;
        	}
        }

        ksort($this->_additionalLists);
    }

	protected function listWithGroups($id){

		$this->_loadListsFullData();

		if(array_key_exists($id, $this->_listsFullData) && $this->_listsFullData[$id]['stats']['grouping_count'] > 0){
			return true;
		}
		return false;
	}

	public function getGroups($id, $store){

		$helper = Mage::helper('mailchimp');
		$store = ($store)? $store : Mage::app()->getStore()->getStoreId();
		$id = ($id)? $id : $helper->getGeneralConfig('general',$store);

		if((bool)$helper->getGeneralConfig('intgr',$store) && $this->listWithGroups($id)){
	        $this->_loadGroups($store,$id);
	        return $this->_groups;
		}
		return false;
	}

	public function getGroupInputs($listId = null, $store, $isAdditional){

		$listId = ($listId)? $listId : Mage::helper('mailchimp')->getGeneralConfig('general',$store);

		$input = '';
		$allValues = '';
		$jsId = ($isAdditional)? $listId : 'is_subscribed';

    	foreach($this->_groups[$listId] as $groupId=>$group){
   			$options = '';
   			$baseValue = '['.$group['name'].'|'.$group['id'].']';
   			$allValues .= $baseValue;

    		foreach($group['groups'] as $item){
    			$type = '';
				$value	= $baseValue.'['.$item['name'].']';
	    		$checked = (isset($item['checked']) && $item['checked'])? 'checked="checked" selected="selected"' : '';

	    		if($group['form_field'] == 'checkboxes'){
					$type = 'checkbox';
					$options .=  '<input type="'.$type.'" '.$checked.' value="' .$value.'" name="group['.$listId.']'.$value.'" title="'.$item['name'].'" class="'.$type.'" onclick="checkParent(\''.$jsId.'\')"/>
		        			<label for="subscription">'.$item['name'].'</label>';
				}elseif($group['form_field'] == 'dropdown'){
					$type = 'select';
					$options .=  '<option value="'.$value.'" '.$checked.'>'.$item['name'].'</option>';
		    	}elseif($group['form_field'] == 'radio'){
					$type = 'radio';
					$options .=  '<input type="'.$type.'" '.$checked.' value="' .$value.'" name="group['.$listId.']'.$baseValue.'[]" title="'.$item['name'].'" class="'.$type.'" onclick="checkParent(\''.$jsId.'\')"/>
	    				<label for="subscription">'.$item['name'].'</label>';
		    	}
	    	}

			if($type == 'select'){
				$opening = '<select class="'.$type.'" name="group['.$listId.']'.$baseValue.'[]" onchange="checkParent(\''.$jsId.'\')" >';
				$blank = '<option value="'.$baseValue.'[]">--Please Select--</option>';
				$options = $opening.$blank.$options.'</select>';
			}
			$header = ($type)? '<div class="title">'.$group['name'].'</div>': '';
			$input .= $header.$options;
    	}

		$allGrps = '<input type="hidden" name="allgroups['.$listId.']" id="allgroups['.$listId.']" value="'.$allValues.'" />';
    	return $input.$allGrps;
    }

	public function isForcedToRegisterSubscribe(){

		$store = Mage::app()->getStore()->getStoreId();
		$helper = Mage::helper('mailchimp');
		$current = Mage::helper('core/url')->getCurrentUrl();

		if((bool)$helper->getSubscribeConfig('forece_register',$store) && (bool)strpos($current, self::CUSTOMER_CREATE_URL)){
			return true;
		}
        return false;
    }

	protected function _loadGroups($store,$id){

        if (empty($this->_groups[$id])) {
            $this->_groups = array();

            $model = Mage::getModel('mailchimp/mailchimp');
			$model->setListId($id);
			$model->setCustomerSession(Mage::getSingleton('customer/session'));
			$groups = $model->getGroupsByListId();
        } else {
            return;
        }
        if( isset($groups) && !is_array($groups)) return;

        foreach ($groups as $item) {
    		$this->_groups[$id][$item['id']] = $item;
        }
        ksort($this->_groups[$id]);
    }

	public function getFormMultiActionUrl(){

        return $this->getUrl('mailchimp/manage/multiSave', array('_secure' => true));
    }

    public function getMainFormInput(){

		$store = Mage::app()->getStore()->getStoreId();
		$val = ($this->getIsSubscribed())? 1 : 0 ;
    	if((bool)Mage::helper('mailchimp')->getGeneralConfig('intgr',$store)){
    		$subA = '<input type="hidden" name="is_general" id="is_general" value="1" />';
			$id = Mage::helper('mailchimp')->getGeneralConfig('general',$store);
    		$a = $subA.'<input name="list['.$id.']" id="list['.$id.']" value="'.$val.'" ';
    	}else{
    		$a = '<input name="is_subscribed" id="subscription" value="1" ';
    	}

		if($val) $a .= ' checked="checked" ';
		$a .= 'class="checkbox" title="'.$this->__('General Subscription') .'" type="checkbox"/>';
		return $a;
    }

}
?>