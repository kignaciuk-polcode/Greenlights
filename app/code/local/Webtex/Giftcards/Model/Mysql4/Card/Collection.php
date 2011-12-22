<?php
class Webtex_Giftcards_Model_Mysql4_Card_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/card');
        parent::_construct();
    }
}