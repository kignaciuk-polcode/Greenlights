<?php

class Polcode_Social_Model_Mysql4_Mails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    public function _construct()
    {
        //parent::_construct();
        $this->_init('social/mails');
    }
    
}
