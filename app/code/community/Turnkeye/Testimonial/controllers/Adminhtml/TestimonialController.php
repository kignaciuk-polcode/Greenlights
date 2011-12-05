<?php

class Turnkeye_Testimonial_Adminhtml_TestimonialController extends Mage_Adminhtml_Controller_Action
{


	protected function initAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('cms/testimonial');
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Testimonial'), Mage::helper('adminhtml')->__('Testimonial'));
	}


	public function indexAction()
	{
		$this->initAction();

		$this->renderLayout();
	}


	public function editAction()
	{
		$this->initAction();

		$this->_addContent($this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit'));
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->editAction();
	}


	public function saveAction()
	{
		if ($this->getRequest()->getPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if (isset($_FILES['testimonial_img']['name']) and (file_exists($_FILES['testimonial_img']['tmp_name']))) {
					$uploader = new Varien_File_Uploader('testimonial_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['testimonial_img']['name']);
					$data['testimonial_img'] = $_FILES['testimonial_img']['name'];
				} else {
					if(isset($data['testimonial_img']['delete']) && $data['testimonial_img']['delete'] == 1) {
						$data['testimonial_img'] = '';
					} else {
						unset($data['testimonial_img']);
					}
				}

				$model = Mage::getModel('testimonial/testimonial');
				$model->setData($data)->setTestimonialId($this->getRequest()->getParam('id'))->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Testimonial was successfully saved'));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}

		$this->_redirect('*/*/');
	}


	public function deleteAction()
	{
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('testimonial/testimonial');
				$model->setTestimonialId($this->getRequest()->getParam('id'))
				      ->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Testimonial was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}


	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('cms/testimonial');
	}


}
