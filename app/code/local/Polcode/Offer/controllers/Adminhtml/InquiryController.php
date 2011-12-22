<?php

class Polcode_Offer_Adminhtml_InquiryController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('offer/inquiry')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Inquires Manager'), Mage::helper('adminhtml')->__('Inquires Manager'));
        return $this;
    }

    protected function _initInquiry() {
        $id = $this->getRequest()->getParam('id');
        $inquiry = Mage::getModel('offer/inquiry')->load($id);

        if (!$inquiry->getId()) {
            $this->_getSession()->addError($this->__('This inquiry no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('offer_inquiry', $inquiry);
        Mage::register('current_inquiry', $inquiry);

        return $inquiry;
    }

    public function indexAction() {
        $this->_initAction();
        //$this->_addContent($this->getLayout()->createBlock('offer/adminhtml_inquiry'));
        $this->renderLayout();
    }

    public function editAction() {
        $inquiryId = $this->getRequest()->getParam('id');

        $inquiryModel = Mage::getModel('offer/inquiry')->load($inquiryId);


        if ($inquiryModel->getId() || $inquiryId == 0) {

            Mage::register('inquiry_data', $inquiryModel);

            $this->loadLayout();
            $this->_setActiveMenu('offer/inquiry');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Inquiry Manager'), Mage::helper('adminhtml')->__('Inquiry Manager'));
            //$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
//           
            $this->_addContent($this->getLayout()->createBlock('offer/adminhtml_inquiry_edit'))
                    ->_addLeft($this->getLayout()->createBlock('offer/adminhtml_inquiry_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('offer/inquiry')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function viewAction() {

        $this->_title($this->__('offer'))->_title($this->__('Inquiries'));

        if ($inquiry = $this->_initInquiry()) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $inquiry->getId()));

            $this->renderLayout();
        }
    }

}