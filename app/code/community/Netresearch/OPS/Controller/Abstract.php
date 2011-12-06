<?php
/**
 * Netresearch_OPS_Controller_Abstract
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return order instance loaded by increment id'
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            $quoteId = $this->getRequest()->getParam('orderID');
            $this->_order = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('quote_id', $quoteId)
                ->getFirstItem();
        }
        return $this->_order;
    }

    /**
     * Get singleton with Checkout by OPS Api
     *
     * @return Netresearch_OPS_Model_Payment_Abstract
     */
    protected function _getApi()
    {
        if (!is_null($this->getRequest()->getParam('orderID'))):
            return $this->_getOrder()->getPayment()->getMethodInstance();
        else:
            return Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
        endif;
    }

    /**
     * get payment helper
     * 
     * @return Netresearch_OPS_Helper_Payment
     */
    protected function getPaymentHelper()
    {
        return Mage::helper('ops/payment');
    }
    
    /**
     * get direct link helper
     * 
     * @return Netresearch_OPS_Helper_Payment
     */
    protected function getDirectlinkHelper()
    {
        return Mage::helper('ops/directlink');
    }

    /**
     * Validation of incoming OPS data
     *
     * @return bool
     */
    protected function _validateOPSData()
    {
        $params = $this->getRequest()->getParams();

        if ($this->isJsonRequested($params)) return true;

        $secureKey = $this->_getApi()->getConfig()->getShaInCode();
        $secureSet = $this->getPaymentHelper()->getSHAInSet($params, $secureKey);

        $helper = Mage::helper('ops');
        $helper->log($helper->__("Incoming Ogone Feedback\n\nRequest Path: %s\nParams: %s\nModule Secureset: %s\nHashed String by Magento: %s\nHashed String by Ogone: %s",
            $this->getRequest()->getPathInfo(),
            Zend_Json::encode($this->getRequest()->getParams()),
            $secureSet,
            Mage::helper('ops/payment')->shaCrypt($secureSet),
            $params['SHASIGN']
        ));
        
        if (Mage::helper('ops/payment')->shaCryptValidation($secureSet, $params['SHASIGN']) !== true) {
            $this->_getCheckout()->addError($this->__('Hash is not valid'));
            return false;
        }

        $order = $this->_getOrder();
        if (!$order->getId()){
            $this->_getCheckout()->addError($this->__('Order is not valid'));
            return false;
        }

        return true;
    }

    public function isJsonRequested($params)
    {
        if (isset($params['PARAMPLUS'])) {
            $data = Zend_Json::decode($params['PARAMPLUS']);
            if ($data['format'] == 'json') {
                return true;
            }
        }
    }
}
