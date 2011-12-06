<?php

class Ebizmarts_Mailchimp_Model_Source_Cms{

    public function getStores(){

    	$storeModel = Mage::getSingleton('adminhtml/system_store');
	   	$options = array();

        foreach ($storeModel->getWebsiteCollection() as $website) {
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    $options[$store->getStoreId()] = $store->getName();
                }
            }
        }

        return $options;
    }

    public function toOptionArray(){

		$allStores = $this->getStores();
		$allOptions = array();

		foreach($allStores as $storeId=>$storeName){
			$allOptions[$storeId] = array('value'=>$storeId,
								   			'label'=>$storeName);
		}
        array_unshift($allOptions, array('value'=>'','label'=> ''));
		return $allOptions;
  	}

    public function getCmssPerStore(){

		$allStores = $this->getStores();
		$allOptions = array();

		foreach($allStores as $storeId=>$storeName){
			$cmss = Mage::getResourceModel('cms/page_collection')->addStoreFilter($storeId)->getItems();
			if(count($cmss)){
				$options = array();
				foreach($cmss as $cms){
					$options[] = array('value'=>$storeId.'_'.$cms->getPageId(),
						   		  			 'label'=>$cms->getTitle());
				}
				$allOptions[] = array('value'=>$options,
								   'label'=>$storeName);
			}
		}
        array_unshift($allOptions, array('value'=>'','label'=> ''));
		return $allOptions;
  	}

}