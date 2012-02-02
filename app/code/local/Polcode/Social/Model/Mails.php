<?php

class Polcode_Social_Model_Mails extends Mage_Core_Model_Abstract
{
    
    const XML_PATH_EMAIL_TEMPLATE   = 'social/email/template';
    const XML_PATH_EMAIL_IDENTITY   = 'sales_email/order/identity';
    
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('social/mails');
    }
    
    public function send() {
        $offerModel = Mage::getModel('sales/order')->load($this->getOrderId());
        
        $email = $offerModel->getCustomerEmail();
        
        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');

        $mailTemplate->setDesignConfig(array(
            'area' => 'frontend',
            'store' => Mage::app()->getStore()->getId()
        ));
        
        $mailTemplate->sendTransactional(
                Mage::helper('social')->getEmailTemplate(), 
                Mage::helper('social')->getEmailSender(), 
                $email,
                null,
                $this->getParams()
        );
        
        $translate->setTranslateInline(true);
    }
    
    
    private function getParams() {
        // TODO
    }
    
}
