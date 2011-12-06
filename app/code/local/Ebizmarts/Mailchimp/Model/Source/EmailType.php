<?php

class Ebizmarts_Mailchimp_Model_Source_EmailType{

    public function toOptionArray(){
        return array(
            array('value'=>'html', 'label'=>Mage::helper('mailchimp')->__('HTML')),
            array('value'=>'text', 'label'=>Mage::helper('mailchimp')->__('TEXT')),
            array('value'=>'mobile', 'label'=>Mage::helper('mailchimp')->__('MOBILE')),
        );
    }
}