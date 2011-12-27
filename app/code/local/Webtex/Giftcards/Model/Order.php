<?php
class Webtex_Giftcards_Model_Order extends Mage_Sales_Model_Order
{
    public function hasGiftCards()
    {
        $items = $this->getItemsCollection();
        foreach ($items as $item) {
            if (Mage::getModel('catalog/product')->load($item->getProductId())->getTypeId() == 'giftcards') {
                return true;
            }
        }
        return false;
    }
}
