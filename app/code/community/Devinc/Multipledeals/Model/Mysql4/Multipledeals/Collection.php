<?php

class Devinc_Multipledeals_Model_Mysql4_Multipledeals_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('multipledeals/multipledeals');
    }
}