<?php
class Webtex_Giftcards_Model_Service_Quote extends Mage_Sales_Model_Service_Quote
{
    public function submitAll()
    {
    	$data = array();
    	$data['giftcard_code'] = $this->getQuote()->getGiftcardCode();
    	$data['giftcard_amount'] = $this->getQuote()->getGiftcardAmount();
    	$this->setOrderData($data);
    	parent::submitAll();
    }
}
