<?php

class Devinc_Multipledeals_Model_Mysql4_Multipledeals extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the multipledeals_id refers to the key field in your database table.
        $this->_init('multipledeals/multipledeals', 'multipledeals_id');
    }
}