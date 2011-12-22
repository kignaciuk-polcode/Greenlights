<?php

class Polcode_Offer_Model_Mysql4_Inquiry_Item extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('offer/inquiry_item', 'inquiry_item_id');
    }

}