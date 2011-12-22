<?php

class Polcode_Offer_InquiryController extends Polcode_Offer_Controller_Abstract {

    public function preDispatch() {
        parent::preDispatch();

        if (!$this->_skipAuthentication && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeInquiryUrl()) {
                Mage::getSingleton('customer/session')->setBeforeInquiryUrl($this->_getRefererUrl());
            }
            Mage::getSingleton('customer/session')->setBeforeInquiryRequest($this->getRequest()->getParams());
        }
        if (!Mage::getStoreConfigFlag('offer/general/active')) {
            $this->norouteAction();
            return;
        }
    }

    public function indexAction() {

        $this->_redirect('*/*/history');
    }

    public function historyAction() {

        $this->loadLayout();

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('offer/session');

        $this->renderLayout();
    }

    public function addAction() {
        $session = Mage::getSingleton('customer/session');
        $inquiry = $this->_getInquiry();


        if (!$inquiry) {
            $this->_redirect('*/*');
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/*');
            return;
        }


        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/*');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeInquiryRequest()) {
                $requestParams = $session->getBeforeOfferRequest();
                $session->unsBeforeInquiryRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $inquiry->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $inquiry->save();

            Mage::dispatchEvent(
                    'inquiry_add_product', array(
                'inquiry' => $inquiry,
                'product' => $product,
                'item' => $result
                    )
            );

            $referer = $session->getBeforeInquirytUrl();
            if ($referer) {
                $session->setBeforeInquirytUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('offer/inquiry')->calculate();

            $message = $this->__('%1$s has been added to your offer inquiry. Click <a href="%2$s">here</a> to continue shopping', $product->getName(), $referer);
            $session->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $session->addError($this->__('An error occurred while adding item to offer inquiry: %s', $e->getMessage()));
        } catch (Exception $e) {
            $session->addError($this->__('An error occurred while adding item to offer inquiry.'));
        }

        $this->_redirect('*');
    }

    public function viewAction() {

        $this->loadLayout();
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('offer/inquiry/history');
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('offer/session');

        $this->renderLayout();
    }

    public function configureAction() {
        $id = (int) $this->getRequest()->getParam('id');
        $inquiry = $this->_getInquiry();
        /* @var $item Polcode_Offer_Model_Inquiry_Item */
        $item = $inquiry->getItem($id);

        try {
            if (!$item) {
                throw new Exception($this->__('Cannot load inquiry item'));
            }

            Mage::register('inquiry_item', $item);

            $params = new Varien_Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);

            $buyRequest = $item->getBuyRequest();

            if (!$buyRequest->getQty() && $item->getProductQty()) {
                $buyRequest->setQty($item->getProductQty());
            }
            if ($buyRequest->getQty() && !$item->getProductQty()) {
                $item->setQty($buyRequest->getQty());
                Mage::helper('offer/inquiry')->calculate();
            }
            $params->setBuyRequest($buyRequest);

            Mage::helper('catalog/product_view')->prepareAndRender($item->getProductId(), $this, $params);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('Cannot configure product'));
            Mage::logException($e);
            $this->_redirect('*');
            return;
        }
    }

    protected function _getInquiry() {
        $inquiry = Mage::registry('inquiry');
        if ($inquiry) {
            return $inquiry;
        }

        try {
            $inquiry = Mage::getModel('offer/inquiry')
                    ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('inquiry', $inquiry);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('offer/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('offer/session')->addException($e, Mage::helper('offer/inquiry')->__('Cannot create inquiry.')
            );
            return false;
        }

        return $inquiry;
    }

    public function removeAction() {
        $inquiry = $this->_getInquiry();
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('offer/inquiry_item')->load($id);

        if ($item->getInquiryId() == $inquiry->getId()) {
            try {
                $item->delete();
                $inquiry->save();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                        $this->__('An error occurred while deleting the item from inquiry: %s', $e->getMessage())
                );
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                        $this->__('An error occurred while deleting the item from inquiry.')
                );
            }
        }

        Mage::helper('offer/inquiry')->calculate();

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }

    public function updateItemOptionsAction() {
        $session = Mage::getSingleton('customer/session');
        $inquiry = $this->_getInquiry();
        if (!$inquiry) {
            $this->_redirect('*/');
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $id = (int) $this->getRequest()->getParam('id');
            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            $inquiry->updateItem($id, $buyRequest)
                    ->save();

            Mage::helper('offer/inquiry')->calculate();
            Mage::dispatchEvent('inquiry_update_item', array(
                'inquiry' => $inquiry, 'product' => $product, 'item' => $inquiry->getItem($id))
            );

            Mage::helper('offer/inquiry')->calculate();

            $message = $this->__('%1$s has been updated in your offer inquiry.', $product->getName());

            $session->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addError($this->__('An error occurred while updating offer inquiry.'));
            Mage::logException($e);
        }

        $this->_redirect('*/*/view', array('inquiry_id' => $id));
    }

    public function updateAction() {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        $post = $this->getRequest()->getPost();
        if ($post && isset($post['qty']) && is_array($post['qty'])) {
            $inquiry = $this->_getInquiry();
            $updatedItems = 0;

            foreach ($post['qty'] as $itemId => $qty) {
                $item = Mage::getModel('offer/inquiry_item')->load($itemId);
                if ($item->getInquiryId() != $inquiry->getId()) {
                    continue;
                }

                // Extract new values


                $qty = $this->_processLocalizedQty($qty);

                if (is_null($qty)) {
                    $qty = $item->getProductQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::getSingleton('customer/session')->addError(
                                $this->__('Can\'t delete item from offer inquiry')
                        );
                    }
                }

                // Check that we need to save
                if ($item->getProductQty() == $qty) {
                    continue;
                }
                try {
                    $item->setProductQty($qty)
                        ->save();
                    $updatedItems++;
                } catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError(
                        $this->__('Can\'t save qty %s', Mage::helper('core')->htmlEscape($description))
                    );
                }
            }
            
            // save wishlist model for setting date of last update
            if ($updatedItems) {
                try {
                    $inquiry->setSubmitted(1)->save();
                    Mage::helper('offer/inquiry')->calculate();
                } catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError($this->__('Can\'t update offer inquiry'));
                }
            }
            
            die();
            
            //@todo add logic to after submit action

            if (isset($post['save_and_submit'])) {
                $this->_redirect('*/*/submit');
                return;
            }
        }
        $this->_redirect('*');
    }

}