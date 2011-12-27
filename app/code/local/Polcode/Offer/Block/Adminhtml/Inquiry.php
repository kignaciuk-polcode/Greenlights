<?php

class Polcode_Offer_Block_Adminhtml_Inquiry extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_controller = 'adminhtml_inquiry';
        $this->_blockGroup = 'offer';
        $this->_headerText = Mage::helper('offer/inquiry')->__('Inquiry Manager');
        parent::__construct();
    }    
    
    
}
