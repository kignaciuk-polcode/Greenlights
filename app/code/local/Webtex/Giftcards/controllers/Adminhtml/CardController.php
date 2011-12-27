<?php
class Webtex_Giftcards_Adminhtml_CardController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('customer/giftcards');
        $this->_addBreadcrumb($this->__('Gift Cards'), $this->__('Gift Cards'));
        $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_card'));
        
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('giftcards/card')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
            
            Mage::register('giftcards_data', $model);
            
            $this->loadLayout();

            $this->_setActiveMenu('customer/giftcards');
            $this->_addBreadcrumb($this->__('Gift Cards'), $this->__('Gift Cards'));

            $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_card_edit'))
                ->_addLeft($this->getLayout()->createBlock('giftcards/adminhtml_card_edit_tabs'));

            $addJs = $this->getLayout()->createBlock('core/text')->setText("
                <script type='text/javascript'>
                    var card_type=$('gift_card_type');
                    function updateForm(){
                        if (card_type.options[card_type.selectedIndex].value == 'E')
                        {
                            $('mail_day2send').up(1).show();
                            $('mail_address').up(1).show();
                        }
                        else if (card_type.options[card_type.selectedIndex].value == 'P')
                        {
                            $('mail_day2send').up(1).hide();
                            $('mail_address').up(1).hide();
                        }
                    }
                    card_type.onchange = function() {updateForm();};
                    updateForm();
                </script>");
            $this->_addJs($addJs);

            $this->renderLayout();
        } else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcards')->__('Card does not exists'));
			$this->_redirect('*/*/');
		}
    }
    
    public function newAction()
    {
        $this->editAction();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            
            $model = Mage::getModel('giftcards/card');
            $model->setData($data);
            $model->setId($this->getRequest()->getParam('id'));
            
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Gift card was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Unable find gift card to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if(($id = $this->getRequest()->getParam('id')) > 0 ) {
            try {
                Mage::getModel('giftcards/card')->load($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Gift card was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function resendAction()
    {
        $params = $this->getRequest()->getParams();
        $cardData = Mage::getResourceModel('giftcards/card_collection');
        $cardData->getSelect()->where('main_table.card_id = ?', $params['id']);
        $cardData->load();
        $storeIds = Mage::app()->getStores();
        $storeId = Mage::app()->getStore(current($storeIds))->getId();

        foreach ($cardData->getItems() as $item) {
      	    if ($item->getGiftCardType() == 'P'){
                $order = Mage::getModel('sales/order')->load($item->getOrderId());
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(false);
                $post = array(
                    'amount'        => Mage::helper('core')->currency($item->getInitialValue(), true, false),
                    'email-to'      => $item->getMailRecipient(),
                    'email-from'    => $item->getMailSender(),
                    'code'          => $item->getCardCode(),
                    'link'          => Mage::getUrl('giftcards/customer/printgiftcard/') . 'id/' . $item->getCardId(),
                    'email-message' => $item->getMailMassege(),
                    'store-phone'   => Mage::getStoreConfig('general/store_information/phone'),
                );
                $postObject = new Varien_Object();
                $postObject->setData($post);
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store'=>$storeId))
                ->sendTransactional(
                Mage::getStoreConfig('giftcards/email/print_template'),
                                    'general',
                $order->getCustomerEmail(),
                null,
                array('data' => $postObject)
                );
                $translate->setTranslateInline(true);
      	    } else {
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(false);
                $post = array(
                    'amount'        => Mage::helper('core')->currency($item->getInitialValue(), true, false),
                    'email-to'      => $item->getMailRecipient(),
                    'email-from'    => $item->getMailSender(),
                    'code'          => $item->getCardCode(),
                    'recipient'     => $item->getMailAddress(),
                    'email-message' => $item->getMailMassege(),
                    'store-phone'   => Mage::getStoreConfig('general/store_information/phone'),
                );
                $postObject = new Varien_Object();
                $postObject->setData($post);
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store'=>$storeId))
                    ->sendTransactional(
                        Mage::getStoreConfig('giftcards/email/email_template'),
                        'general',
                        $item->getMailAddress(),
                        null,
                        array('data' => $postObject)
                    );
                $translate->setTranslateInline(true);
      	    }
        }
        $this->_redirect('*/*/');
    }


    public function massDeleteAction()
    {
        $cardIds = $this->getRequest()->getParam('card');
        if (!is_array($cardIds)) {
            $this->_getSession()->addError($this->__('Please select gift card(s)'));
        }
        else {
            try
            {
                $card = Mage::getSingleton('giftcards/card');
                foreach ($cardIds as $cardId) {
                    $card->setId($cardId)->delete();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($cardIds))
                );
            }
            catch (Exception $e)
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
}