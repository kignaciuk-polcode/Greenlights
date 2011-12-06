<?php

class Ebizmarts_Mailchimp_Adminhtml_CtemplateController extends Mage_Adminhtml_Controller_Action{

	public function indexAction(){

        $this->loadLayout()
        	->_setActiveMenu('newsletter')
        	->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate'))
        	->renderLayout();
    }

	public function newAction() {

        $this->loadLayout()
        	->_setActiveMenu('newsletter');

		//$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate_edit'))
			 ->_addLeft($this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate_edit_tabs'))
			 ->renderLayout();
	}

	public function editAction() {

		$params = $this->getRequest()->getParams();
		if(!is_array($params) || !isset($params['id'],$params['tid'])){
			$this->_redirect('*/*/');
			return;
		}

		$helper = Mage::helper('mailchimp');
		$mdl = Mage::getModel('mailchimp/mailchimp');
		$mdl->setTemplateId($params['id']);
		$mdl->setTid($params['tid']);
		$template = $mdl->getTemplateInfo();

		if($template && $template->getId()){
			$col = $helper->getCtemplatesCollection();
			foreach($col as $item){
				if($item->getId() == $mdl->getTemplateId() && $item->getTid() == $mdl->getTid()){
					$template->addData($item->getData());
					$template->setExists(true);
					break;
				}
			}

			if(!$template->getExists()){
				Mage::getSingleton('adminhtml/session')->addError($helper->__('Template does not exist'));
				$this->_redirect('*/*/');
				return;
			}

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
					$template->setData($data);
			}

			$allOptions = array();
			foreach($template->getDefaultContent() as $k=>$v){
				$template->setData($k,trim($v));
				$allOptions[] = array('value'=>$k,'label'=>$k);
			}
			array_unshift($allOptions, array('value'=>'','label'=> ''));
			$template->setDefaultContentSections($allOptions);
			$template->setOriginalSource($template->getSource());
			$template->setUpdateSection($helper->__('Update HTML source field with the selected section'));

			Mage::register('ctemplate_data', $template);

	        $this->loadLayout()
	        	->_setActiveMenu('newsletter');

			//$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate_edit'))
				 ->_addLeft($this->getLayout()->createBlock('mailchimp/adminhtml_ctemplate_edit_tabs'))
				 ->renderLayout();
		}else{
			Mage::getSingleton('adminhtml/session')->addError($helper->__('Template does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function massDeactivateAction() {

		$campaignTemplates = $this->getRequest()->getParam('ctemplates');
        if(!is_array($campaignTemplates)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        }else{
        	$helper = Mage::helper('mailchimp');
        	$apikey = $helper->getApiKey();
			if($apikey){

				$items = $helper->getCtemplatesCollection()->getItems();

				foreach($campaignTemplates as $k=>$template){
					if(!array_key_exists($template,$items) || $items[$template]->getTid() != 'user' || $items[$template]->getActive() == 'N'){
						unset($campaignTemplates[$k]);
					}
				}
				$count = 0;
				if(count($campaignTemplates)){
					$mdl = Mage::getModel('mailchimp/mailchimp');
					$mdl->MCAPI($apikey);
					foreach ($campaignTemplates as $template) {
						$mdl->templateDel($template);
						if ($mdl->errorCode){
							$mdl->setCode($mdl->errorCode);
							$mdl->setMessage($mdl->errorMessage);
							$helper->addException($mdl);
						}else{
							$count++;
						}
		            }
				}
				if($count){
					Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Total of %d template(s) were successfully deactivated', $count));
				}else{
					Mage::getSingleton('adminhtml/session')->addNotice($helper->__('You can only deactivate \'user\' type and/or activated templates.'));
				}

			}
        }
        $this->_redirect('*/*/index');
    }

	public function massReactivateAction() {

        $campaignTemplates = $this->getRequest()->getParam('ctemplates');
        if(!is_array($campaignTemplates)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        }else{
        	$helper = Mage::helper('mailchimp');
        	$apikey = $helper->getApiKey();
			if($apikey){

				$items = $helper->getCtemplatesCollection()->getItems();

				foreach($campaignTemplates as $k=>$template){
					if(!array_key_exists($template,$items) || $items[$template]->getTid() != 'user' || $items[$template]->getActive() == 'Y'){
						unset($campaignTemplates[$k]);
					}
				}
				$count = 0;
				if(count($campaignTemplates)){
					$mdl = Mage::getModel('mailchimp/mailchimp');
					$mdl->MCAPI($apikey);
					foreach ($campaignTemplates as $template) {
						$mdl->templateUndel($template);
						if ($mdl->errorCode){
							$mdl->setCode($mdl->errorCode);
							$mdl->setMessage($mdl->errorMessage);
							$helper->addException($mdl);
						}else{
							$count++;
						}
		            }
				}
				if($count){
					Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Total of %d template(s) were successfully reactivated', $count));
				}else{
					Mage::getSingleton('adminhtml/session')->addNotice($helper->__('You can only reactivate \'user\' type and/or deactivated templates.'));
				}

			}
        }
        $this->_redirect('*/*/index');
    }

	public function deleteAction() {

        $templateId = $this->getRequest()->getParam('id');
        if(!$templateId){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        }else{
        	$apikey = Mage::helper('mailchimp')->getApiKey();
			if($apikey){
				$mdl = Mage::getModel('mailchimp/mailchimp');
				$mdl->MCAPI($apikey);

				$mdl->templateDel($templateId);
				if ($mdl->errorCode){
					$mdl->setCode($mdl->errorCode);
					$mdl->setMessage($mdl->errorMessage);
					Mage::helper('mailchimp')->addException($mdl);
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mailchimp')->__('The template have been deactivated'));
				}
			}
        }
        $this->_redirect('*/*/index');
    }

	public function reactivateAction() {

        $templateId = $this->getRequest()->getParam('id');
        if(!$templateId){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        }else{
        	$apikey = Mage::helper('mailchimp')->getApiKey();
			if($apikey){
				$mdl = Mage::getModel('mailchimp/mailchimp');
				$mdl->MCAPI($apikey);
				$mdl->templateUndel($templateId);
				if ($mdl->errorCode){
					$mdl->setCode($mdl->errorCode);
					$mdl->setMessage($mdl->errorMessage);
					Mage::helper('mailchimp')->addException($mdl);
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mailchimp')->__('The template have been reactivated'));
				}
			}
        }
        $this->_redirect('*/*/index');
    }

	public function saveAction() {

		$params = $this->getRequest()->getParams();
		if($params['name'] && $params['source']){
        	$apikey = Mage::helper('mailchimp')->getApiKey();
			if($apikey){
				$mdl = Mage::getModel('mailchimp/mailchimp');
				$mdl->MCAPI($apikey);
				$mdl->setName(substr($params['name'],0,49));
				$mdl->setHtml((string)$params['source']);
				$templateId = (isset($params['id']))? $params['id'] : '';
		        if($templateId){
					$mdl->templateUpdate($templateId,
										 array('name'=>$mdl->getName(),
											   'html'=>$mdl->getHtml()
										 ));
		        }else{
					$templateId = $mdl->templateAdd($mdl->getName(),
													$mdl->getHtml()
												    );
		        }
				if ($mdl->errorCode){
					$mdl->setCode($mdl->errorCode);
					$mdl->setMessage($mdl->errorMessage);
					Mage::helper('mailchimp')->addException($mdl);
					if($templateId){
						$this->_redirect('*/*/edit', array('id' =>$templateId,'tid' =>'user'));
					}else{
						$this->_redirect('*/*/');
					}
					return;
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mailchimp')->__('The template have been saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
					if (isset($params['back'])) {
						$this->_redirect('*/*/edit', array('id' =>$templateId,'tid' =>'user'));
						return;
					}
					$this->_redirect('*/*/');
					return;
				}
			}
        }
        $this->_redirect('*/*/');
    }

    public function cmsAction() {

		if ($this->getRequest()->isXmlHttpRequest()) {
			$params = $this->getRequest()->getParams();
			$helper = Mage::helper('mailchimp');
			if(isset($params['cms_id'])){
				$store = $params['store_id'];
				$page = ($pageUrl = $helper->getPageUrl($params['cms_id'],$store))? file_get_contents($pageUrl, false) : '';
				if($page){
				    preg_match_all('/(href=")(\w.*\.css)"/i',$page,$csss);
					if(count($csss[2])){
					    foreach($csss[2] as $sheet){
					    	if(strstr($sheet,'styles.css')){
					    		$page = str_replace($sheet,"",$page);
					    		$skinFolder = Mage::getDesign()->getSkinUrl(null,
					    													array('_store'=>$store,
						    													  '_area'=>'frontend',
							    												  '_package'=>Mage::getStoreConfig('design/package/name',$store),
							    												  '_default'=>Mage::getStoreConfig('design/theme/default',$store)));
							    $styles = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', str_replace('../',$skinFolder,file_get_contents($sheet, true)));
							    $styles= str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $styles);
						    	$page = str_replace("</head>","<style type='text/css'>".$styles."</style></head>",$page);
	    	        			$apikey = $helper->getApiKey();
								if(isset($params['cms_inlined']) && $apikey){
									$mdl = Mage::getModel('mailchimp/mailchimp');
									$mdl->MCAPI($apikey);
									$inlinedPage = $mdl->inlineCss($page,true);
									if ($mdl->errorCode){
										$mdl->setCode($mdl->errorCode);
										$mdl->setMessage($mdl->errorMessage);
										Mage::helper('mailchimp')->addException($mdl);
									}else{
										 $page = $inlinedPage;
									}
								}
						    	break;
					    	}
					    }
					}
				}
		        $this->getResponse()->setBody($page);
			}
        }
		return;
    }
}