<?php

abstract class Polcode_Offer_Controller_Abstract extends Mage_Core_Controller_Front_Action
{

    protected $_localFilter = null;


    protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter) {
            $this->_localFilter = new Zend_Filter_LocalizedToNormalized(array('locale' => Mage::app()->getLocale()->getLocaleCode()));
        }
        $qty = $this->_localFilter->filter($qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }


    abstract protected function _getInquiry();

}
