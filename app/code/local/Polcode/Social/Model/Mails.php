<?php

class Polcode_Social_Model_Mails extends Mage_Core_Model_Abstract
{
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('social/mails');
    }
    
}
