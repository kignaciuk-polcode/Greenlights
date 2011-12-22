<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_View_Form extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('offer/inquiry/view/form.phtml');
    }
}
