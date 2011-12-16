<?php

class Polcode_Social_Model_Mysql4_Mails extends Mage_Core_Model_Mysql4_Abstract
{
    
    public function _construct()
    {
        $this->_init('social/mails', 'social_mails_id');
    }
    
}
