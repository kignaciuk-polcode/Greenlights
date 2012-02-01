<?php

class Polcode_Social_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_EMAIL_TEMPLATE   = 'social/email/template';
    const XML_PATH_EMAIL_IDENTITY   = 'social/email/identity';
    const XML_PATH_EMAIL_ENABLED   = 'social/email/enabled';
    

    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $store);
    }
    
    public function getEmailSender($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $store);
    }      
    
    
    public function isEnabled($store = null){
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED, $store);
    }
    
}
