<?php

class Webtex_Giftcards_Block_Printgiftcards extends Mage_Core_Block_Template
{
	public function _getGiftcard(){
		$custometId = Mage::getSingleton('customer/session')->getCustomerId();
		$customer = Mage::getModel('customer/customer')->load($custometId);
		$giftcardId = (int) $this->getRequest()->getParam('id');
		$cardData = Mage::getResourceModel('giftcards/card_collection');
		$cardData->getSelect()->where('main_table.card_id = ?', $giftcardId);
		$cardData->getSelect()->where('main_table.mail_address = ?', $customer->getEmail());
		$cardData->load();
		foreach ($cardData->getItems() as $date){
			return $date;
		}
		return $this;
	}
}