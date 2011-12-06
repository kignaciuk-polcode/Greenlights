<?php

class Ebizmarts_Mailchimp_Model_Source_BulkType{

    public function toOptionArray(){
        return array(
        	Ebizmarts_Mailchimp_Model_BulkSynchro::WAY_IMPORT => Mage::helper('mailchimp')->__('Import'),
        	Ebizmarts_Mailchimp_Model_BulkSynchro::WAY_EXPORT => Mage::helper('mailchimp')->__('Export')
        );
    }
}