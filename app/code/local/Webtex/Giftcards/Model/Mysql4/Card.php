<?php
class Webtex_Giftcards_Model_Mysql4_Card extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/card', 'card_id');
    }
}