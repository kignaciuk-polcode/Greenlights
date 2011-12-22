<?php
class Webtex_Giftcards_CustomerController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}

	public function giftcardPostAction()
	{
        if (!Mage::helper('customer')->isLoggedIn()) {
			Mage::getSingleton('customer/session')->authenticate($this);
			return;
		}
		$giftcardCode = (string) $this->getRequest()->getParam('giftcard_code');
        $giftcardCode = trim($giftcardCode);
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
		$cardData = Mage::getResourceModel('giftcards/card_collection');
		$cardData->getSelect()
                ->where('card_code = ?', $giftcardCode)
		        ->where('status = ?', 'A');
		foreach ($cardData->getItems() as $card) {
            $card->activateCardForCustomer($customerId);
		}
		$this->_redirect('giftcards/customer/balance');
		return;
	}

	public function balanceAction()
	{
		if (!Mage::helper('customer')->isLoggedIn()) {
			Mage::getSingleton('customer/session')->authenticate($this);
			return;
		}
		$this->loadLayout(array('default'));
		$this->renderLayout();
	}

    public function printGiftcardAction()
    {
    	if (!Mage::helper('customer')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->addError("To print gift card  you need to be logged in");
			Mage::getSingleton('customer/session')->authenticate($this);
			return;
		}
    	$customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
    	$giftcardId = (int) $this->getRequest()->getParam('id');
    	$cardData = Mage::getResourceModel('giftcards/card_collection');
		$cardData->getSelect()->where('main_table.card_id = ?', $giftcardId);
		$cardData->getSelect()->where('main_table.mail_address = ?', $customer->getEmail());
		$cardData->load();

		if(count($cardData->getItems()) == '0'){
			$this->_redirect('/');
			return;
		}
		$this->loadLayout('print');
		$this->renderLayout();

    	return $this;
    }
}