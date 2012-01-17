<?php

class Devinc_Multipledeals_Adminhtml_MultipledealsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('multipledeals/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('multipledeals/multipledeals')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('multipledeals_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('multipledeals/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit'))
				->_addLeft($this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('multipledeals')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
    public function productsAction()
    {
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_tab_products')->toHtml()
        );
		
    }
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function mainAction() {
		$prev_main_deal_id = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('type', array('eq' => 1))->setOrder('multipledeals_id', 'DESC')->getFirstItem()->getId();
		$model = Mage::getModel('multipledeals/multipledeals');	
		$model->setId($prev_main_deal_id)
			  ->setType('2')
			  ->save();
			  
		$new_main_deal_id = $this->getRequest()->getParam('id');		
		
		$model = Mage::getModel('multipledeals/multipledeals');	
		$model->setId($new_main_deal_id)
			  ->setType('1')
			  ->save();		
		
		$this->_redirect('*/*/');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$data = $this->_filterPostData($data);  			
	  		
			if (!isset($data['deal_qty'])) {
				$data['deal_qty'] = 0;
			}
			
			if (!isset($data['deal_price'])) {
				$data['deal_price'] = 0;
			}
			
			$dataTimeFrom = $data['time_from'][0].','.$data['time_from'][1].','.$data['time_from'][2];
			$dataTimeTo = $data['time_to'][0].','.$data['time_to'][1].','.$data['time_to'][2];
			$data['time_from'] = $dataTimeFrom;
			$data['time_to'] = $dataTimeTo;	
			
			$multidealsType = Mage::getModel('multipledeals/multipledeals')->load($this->getRequest()->getParam('id'))->getType();	
			if ($multidealsType!='') {
				$data['type'] = $multidealsType;					
			}
			
			$model = Mage::getModel('multipledeals/multipledeals');	
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {				
				
				$model->save();
				
				
				$_product = Mage::getModel('catalog/product')->load($model->getProductId());	
				$stockItem = $_product->getStockItem();
				if ($stockItem->getIsInStock()) {
					if ($_product->getTypeId()=='simple' || $_product->getTypeId()=='virtual' || $_product->getTypeId()=='downloadable') {
						if ($model->getDealQty()>0) {
							$in_stock = true;
						} else {
							$in_stock = false;						
						}
					} else {
						$in_stock = true;						
					}
				} else {
					$in_stock = false;
				}
				
				$product_status = Mage::getModel('catalog/product')->load($model->getProductId())->getStatus();
			
				if ($in_stock && $product_status==1) {
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('multipledeals')->__('Deal was successfully saved'));
				} elseif ($product_status!=1 && $model->getProductId()!=0) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because the product is disabled.'));		
					$model->setId($model->getId())
					  ->setStatus('2')
					  ->save();			
				} elseif (!$in_stock) {
					if ($stockItem->getIsInStock()) {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because the deal\'s qty is 0.'));		
					} else {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because the product is out of stock.'));		
					}
					$model->setId($model->getId())
					  ->setStatus('2')
					  ->save();			
				} elseif ($model->getProductId()==0) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because no product was assigned.'));					
				}
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					Mage::getModel('multipledeals/multipledeals')->refreshDeals();
					$selected_product_id = $model->getProductId();
					$visibility = array(2, 4);
					$collection = Mage::getModel('catalog/product')->getCollection()->setOrder('entity_id', 'DESC')->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter('entity_id', array('gteq' => $selected_product_id));
					
					$page_nr = ceil(count($collection)/20);
					$this->_redirect('*/*/edit', array('id' => $model->getId(), 'page' => $page_nr));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('multipledeals')->__('Unable to find deal to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('multipledeals/multipledeals');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $multipledealsIds = $this->getRequest()->getParam('multipledeals');
        if(!is_array($multipledealsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($multipledealsIds as $multipledealsId) {
                    $multipledeals = Mage::getModel('multipledeals/multipledeals')->load($multipledealsId);
                    $multipledeals->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($multipledealsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $multipledealsIds = $this->getRequest()->getParam('multipledeals');
        if(!is_array($multipledealsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($multipledealsIds as $multipledealsId) {
                    $multipledeals = Mage::getSingleton('multipledeals/multipledeals')
                        ->load($multipledealsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($multipledealsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'multipledeals.csv';
        $content    = $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, strip_tags($content));
    }

    public function exportXmlAction()
    {
        $fileName   = 'multipledeals.xml';
        $content    = $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }	
	
	protected function _filterPostData($data)
    {
        $data = $this->_filterDatesCustom($data, array('date_from', 'date_to'));
        return $data;
    }
	
	protected function _filterDatesCustom($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
		
        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $this->LocalizedToNormalized($array[$dateField]);
                $array[$dateField] = $this->NormalizedToLocalized($array[$dateField]);
            }
        }
        return $array;
    }
	
	public function LocalizedToNormalized($value)
    {
		if (substr(Mage::app()->getLocale()->getLocaleCode(),0,2)!='en') {
		    $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
		  		Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
		    );
	    } else {		
		    $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
		  		Mage_Core_Model_Locale::FORMAT_TYPE_LONG
		    );
	    }
	
		$_options = array(
			'locale'      => Mage::app()->getLocale()->getLocaleCode(),
			'date_format' => $dateFormatIso,
			'precision'   => null
		);
        return Zend_Locale_Format::getDate($value, $_options);        
    }
	
	public function NormalizedToLocalized($value)
    {
        #require_once 'Zend/Date.php';
        $date = new Zend_Date($value, Mage::app()->getLocale()->getLocaleCode());
        return $date->toString('yyyy-MM-dd');       
    }
}