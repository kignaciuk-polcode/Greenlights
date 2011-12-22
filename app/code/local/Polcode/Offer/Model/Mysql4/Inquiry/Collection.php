<?php

class Polcode_Offer_Model_Mysql4_Inquiry_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        //parent::__construct();
        $this->_init('offer/inquiry');
    }
        
}