<?php

class Ebizmarts_Mailchimp_Adminhtml_BulkSyncController extends Mage_Adminhtml_Controller_Action{

	public function indexAction() {

    	$this->loadLayout()
			 ->_setActiveMenu('newsletter')
        	 ->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_bulkSync'))
          	 ->renderLayout();
	}

	public function newAction() {

		$params = $this->getRequest()->getParams();
		if(isset($params['way'],$params['list'],$params['isAjax'])){
			$params['start'] = (is_nan($params['start']) || $params['start'] < 0)? (int)0 : $params['start'] ;
			$params['limit'] = (is_nan($params['limit']) || $params['limit'] < 0 || $params['limit'] > 15000)? (int)15000 : $params['limit'] ;
			$file = Mage::getModel('mailchimp/BulkSynchro')
	            ->setType($params['way'])
	            ->setList($params['list'])
	            ->setStore($params['store'])
	            ->setTime(time());
			try {
	  			$file->create($params);
  				$this->_getSession()->addSuccess(Mage::helper('mailchimp')->__('A new file has been created.'));
	        }catch (Exception  $e) {
	        	Mage::helper('mailchimp')->addException($e);
	        }
		}
	}

	public function deleteAction() {

		$params = $this->getRequest()->getParams();

		if(isset($params['time'],$params['type'],$params['list'],$params['store'])){
			$file = Mage::getModel('mailchimp/BulkSynchro')
	            ->setTime((int)$params['time'])
	            ->setType($params['type'])
	            ->setStore($params['store'])
            	->setList($params['list']);
			try {
	  			$file->delete();
  				$this->_getSession()->addSuccess(Mage::helper('mailchimp')->__('The file has been deleted.'));
	        }catch (Exception  $e) {
	        	Mage::helper('mailchimp')->addException($e);
	        }
		}
		$this->_redirect('*/*/');
	}

	public function downloadAction() {

		$params = $this->getRequest()->getParams();
		if(isset($params['time'],$params['type'],$params['list'],$params['store'])){
			$file = Mage::getModel('mailchimp/BulkSynchro')
	            ->setTime((int)$params['time'])
	            ->setType($params['type'])
	            ->setStore($params['store'])
	            ->setList($params['list']);
	        if (!$file->exists()) {
	            $this->_redirect('*/*');
	        }
			try {
		        $this->_prepareDownloadResponse($file->getFileName(), null, 'application/octet-stream', $file->getSize());
		        $this->getResponse()->sendHeaders();
		        $file->output();
		        exit();
  				$this->_getSession()->addSuccess(Mage::helper('mailchimp')->__('The file has been downloaded.'));
	        }catch (Exception  $e) {
	        	Mage::helper('mailchimp')->addException($e);
	        }
		}
		$this->_redirect('*/*/');
	}

	public function runAction() {

		$params = $this->getRequest()->getParams();
		$helper = Mage::helper('mailchimp');

		if(isset($params['time'],$params['type'],$params['list'],$params['store'])){
			try {
				$file = Mage::getModel('mailchimp/BulkSynchro')
		            ->setTime((int)$params['time'])
		            ->setType($params['type'])
		            ->setStore($params['store'])
		            ->setList($params['list']);
		        if (!$file->exists()) {
		            $this->_redirect('*/*');
		        }

				$s = $file->run();

				if(isset($s['chimp'])) $this->_getSession()->addSuccess($helper->__('%s subscribers has been registered/updated on the MailChimp Synchronized Subscribers List.',$s['chimp']));
				if(isset($s['general'])) $this->_getSession()->addSuccess($helper->__('%s subscribers has been registered/updated on the Magento General List.',$s['general']));
				if(isset($s['success_count']) && $s['success_count']) $this->_getSession()->addSuccess($helper->__('%s subscribers has been Unsubscripted on MailChimp.',$s['success_count']));
				if(isset($s['add_count']) && $s['add_count']) $this->_getSession()->addSuccess($helper->__('%s subscribers has been registered on MailChimp.',$s['add_count']));
				if(isset($s['update_count']) && $s['update_count']) $this->_getSession()->addSuccess($helper->__('%s subscribers has been updated on MailChimp.',$s['update_count']));
				if(isset($s['error'])) $this->_getSession()->addError($helper->__('Something went wrong, internal message: %s',$s['error']));
				if(isset($s['notice'])) $this->_getSession()->addNotice($helper->__('%s',$s['notice']));

	        }catch (Exception  $e) {
	        	$helper->addException($e);
	        }
		}
		$this->_redirect('*/*/');
	}
}