<?php
/**
 * Netresearch_OPS_Model_Payment_Cc
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Model_Payment_Cc
    extends Netresearch_OPS_Model_Payment_Abstract
{
    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** info source path */
    protected $_infoBlockType = 'ops/info_cc';

    /** payment code */
    protected $_code = 'ops_cc';

    /** ops payment code */
    public function getOpsCode($payment=null) {
        if ('PostFinance + card' == $this->getOpsBrand($payment)) {
            return 'PostFinance Card';
        }
        if ('UNEUROCOM' == $this->getOpsBrand($payment)) {
            return 'UNEUROCOM';
        }
        return 'CreditCard';
    }

    public function getOpsBrand($payment=null) {
        if (is_null($payment)) {
            $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
        }
        return $payment->getAdditionalInformation('CC_BRAND');
    }

    public function getOrderPlaceRedirectUrl($payment=null)
    {
        if ($this->hasBrandAliasInterfaceSupport($payment)) {
            if ('' == $this->getOpsHtmlAnswer($payment)) 
                return false; // Prevent redirect on cc payment
            else 
                return Mage::getModel('ops/config')->get3dSecureRedirectUrl();
        }
        return parent::getOrderPlaceRedirectUrl();
    }
    
    public function getOpsHtmlAnswer($payment=null) {
        if (is_null($payment)) {
            $order = Mage::getModel('sales/order')->loadByAttribute('quote_id', Mage::getSingleton('checkout/session')->getQuote()->getId());
            $payment = $order->getPayment();
        }
        return $payment->getAdditionalInformation('HTML_ANSWER');
    }

    /**
     * only some brands are supported to be integrated into onepage checkout
     * 
     * @return array
     */
    public function getBrandsForAliasInterface()
    {
        return array(
            'American Express',
            'Billy',
            'Diners Club',
            'MaestroUK',
            'MasterCard',
            'VISA',
        );
    }

    /**
     * if cc brand supports ops alias interface
     * 
     * @param Mage_Payment_Model_Info $payment 
     *
     * @return void
     */
    public function hasBrandAliasInterfaceSupport($payment=null)
    {
        return in_array(
            $this->getOpsBrand($payment),
            $this->getBrandsForAliasInterface()
        );
    }
}

