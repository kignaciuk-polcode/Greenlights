<?php
class Webtex_Giftcards_Block_Balance extends Mage_Core_Block_Template
{
    public function getCurrentBalance()
    {
        $balance = 0;
        
        $cardData = Mage::getResourceModel('giftcards/card_collection');
        $cardData->getSelect()->where('main_table.status = ?', 'I');
        $cardData->getSelect()->where('main_table.customer_id = ?', Mage::getSingleton('customer/session')->getCustomerId());
        $cardData->load();

        foreach ($cardData->getItems() as $value) {
            $balance += $value->getCurrentBalance();
        }

        return $balance;
    }
}
