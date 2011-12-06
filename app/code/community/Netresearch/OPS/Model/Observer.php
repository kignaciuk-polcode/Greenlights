<?php
class Netresearch_OPS_Model_Observer
{
    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function getHelper($name=null)
    {
        if (is_null($name)) {
            return Mage::helper('ops');
        }
        return Mage::helper('ops/' . $name);
    }

    /**
     * trigger ops payment
     */
    public function checkoutTypeOnepageSaveOrderBefore($observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $code = $quote->getPayment()->getMethodInstance()->getCode();

        try {
            if ('ops_cc' == $code && $quote->getPayment()->getMethodInstance()->hasBrandAliasInterfaceSupport($quote->getPayment(), 1)) {
                $this->confirmCcPayment($order, $quote);
            } elseif ('ops_directDebit' == $code) {
                $this->confirmDdPayment($order, $quote);
            }
        } catch (Exception $e) {
            $quote->setIsActive(true);
            $this->getOnepage()->getCheckout()->setGotoSection('payment');
            throw new Mage_Core_Exception($e->getMessage());
        }
    }

    public function salesModelServiceQuoteSubmitSuccess($observer)
    {
        $quote = $observer->getQuote();
        if (true === $this->isCheckoutWithCcOrDd($quote->getPayment()->getMethodInstance()->getCode())) {
            $quote = $observer->getQuote();
            $quote->getPayment()
                ->setAdditionalInformation('checkoutFinishedSuccessfully', true)
                ->save();
        }
    }

    /**
     * set order status for orders with OPS payment
     */
    public function checkoutTypeOnepageSaveOrderAfter($observer)
    {
        $quote = $observer->getQuote();
        if (true === $this->isCheckoutWithCcOrDd($quote->getPayment()->getMethodInstance()->getCode())) {
            $order = $observer->getOrder();
    
            /* if there was no error */
            if (true === $quote->getPayment()->getAdditionalInformation('checkoutFinishedSuccessfully')) {
                $opsResponse = $quote->getPayment()->getAdditionalInformation('ops_response');
                if ($opsResponse) {
                    Mage::helper('ops/payment')->applyStateForOrder($order, $opsResponse);
                }
            } else {
                $this->handleFailedCheckout($quote, $order);
            }
        }
    }

    public function salesModelServiceQuoteSubmitFailure($observer)
    {
        $quote = $observer->getQuote();
        if (true === $this->isCheckoutWithCcOrDd($quote->getPayment()->getMethodInstance()->getCode())) {
            $this->handleFailedCheckout(
                $observer->getQuote(),
                $observer->getOrder()
            );
        }
    }

    public function handleFailedCheckout($quote, $order)
    {
        if (true === $this->isCheckoutWithCcOrDd($quote->getPayment()->getMethodInstance()->getCode())) {
            $opsResponse = $quote->getPayment()->getAdditionalInformation('ops_response');
            if ($opsResponse) {
                $this->getHelper()->log('Cancel Ogone Payment because Order Save Process failed.');
                
                //Try to cancel order only if the payment was ok
                if (Mage::helper('ops/payment')->isPaymentAccepted($opsResponse['STATUS'])) {
                    if (true === $this->getHelper('payment')->isPaymentAuthorizeType($opsResponse['STATUS'])) { //do a void
                        $params = array (
                            'OPERATION' => Netresearch_OPS_Model_Payment_Abstract::OPS_DELETE_AUTHORIZE_AND_CLOSE,
                            'ORDERID' => $quote->getId(),
                            'AMOUNT' => $quote->getGrandTotal() * 100
                        );
                    }
                    
                    if (true === $this->getHelper('payment')->isPaymentCaptureType($opsResponse['STATUS'])) { //do a refund
                        $params = array (
                            'OPERATION' => Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL,
                            'ORDERID' => $quote->getId(),
                            'AMOUNT' => $quote->getGrandTotal() * 100
                        );
                    }
                    $url = Mage::getModel('ops/config')->getDirectLinkGatewayOrderPath();
                    Mage::getSingleton('ops/api_directlink')->performRequest($params, $url);
                }
            }
        }
    }

    protected function getQuoteCurrency($quote)
    {
        if ($quote->hasForcedCurrency()){
            return $quote->getForcedCurrency()->getCode();
        } else {
            return $quote->getStore()->getCurrentCurrency()->getCode();
        }
    }

    public function confirmCcPayment($order, $quote)
    {
        $alias = $quote->getPayment()->getAdditionalInformation('alias');
        $cn = $quote->getPayment()->getAdditionalInformation('CC_CN');

        $requestParams = array(
            'ALIAS'            => $alias,
            'CN'               => $cn,
            'AMOUNT'           => $quote->getGrandTotal() * 100,
            'CURRENCY'         => $this->getQuoteCurrency($quote),
            'OPERATION'        => $this->_getPaymentAction($quote),
            'ORDERID'          => $quote->getId(),
            'REMOTE_ADDR'      => $order->getRemoteIp(),
            'EMAIL'            => $order->getCustomerEmail()
        );
        
        $requestParams3ds = array();
        if (Mage::getModel('ops/config')->get3dSecureIsActive()) {
            $requestParams3ds = array(
                'FLAG3D'           => 'Y',
                'WIN3DS'           => Netresearch_OPS_Model_Payment_Abstract::OPS_DIRECTLINK_WIN3DS,
                'LANGUAGE'         => Mage::app()->getLocale()->getLocaleCode(),
                'HTTP_ACCEPT'      => '*/*',
                'HTTP_USER_AGENT'  => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)',
                'ACCEPTURL'        => Mage::getModel('ops/config')->getAcceptUrl(),
                'DECLINEURL'       => Mage::getModel('ops/config')->getDeclineUrl(),
                'EXCEPTIONURL'     => Mage::getModel('ops/config')->getExceptionUrl(),
            );
        }
        $requestParams = array_merge($requestParams, $requestParams3ds);

        return $this->performDirectLinkRequest($quote, $requestParams); 
    }

    public function confirmDdPayment($order, $quote)
    {
        $cn = $quote->getPayment()->getAdditionalInformation('CN');

        $requestParams = array(
            'AMOUNT'    => $quote->getGrandTotal() * 100,
            'CARDNO'    => $quote->getPayment()->getAdditionalInformation('CARDNO'),
            'CURRENCY'  => $this->getQuoteCurrency($quote),
            'CN'        => $cn,
            'ED'        => '9999', // Always the same on direct debit
            'OPERATION' => $this->_getPaymentAction($quote),
            'ORDERID'   => $quote->getId(),
            'PM'        => $quote->getPayment()->getAdditionalInformation('PM'),
        );

        return $this->performDirectLinkRequest($quote, $requestParams); 
    }

    public function performDirectLinkRequest($quote, $params)
    {
        $url = Mage::getModel('ops/config')->getDirectLinkGatewayOrderPath();
        $response = Mage::getSingleton('ops/api_directlink')->performRequest($params, $url);
        if (Mage::helper('ops/payment')->isPaymentFailed($response['STATUS'])) {
            throw new Mage_Core_Exception('Ogone Payment failed');
        }
        $quote->getPayment()->setAdditionalInformation('ops_response', $response)->save();
    }

    /**
     * Check if checkout was made with OPS CreditCart or DirectDebit
     * 
     * @return boolean
     */
    protected function isCheckoutWithCcOrDd($code)
    {
        if ('ops_cc' == $code || 'ops_directDebit' == $code)
            return true;
        else
            return false;
    }
    
    /**
     * get payment operation code
     * 
     * @param Mage_Sales_Model_Order $order 
     *
     * @return string
     */
    public function _getPaymentAction($order)
    {
        $operation = 'RES';

        // different capture operation name for direct debits
        if ('Direct Debits DE' == $order->getPayment()->getAdditionalInformation('PM')
            || 'Direct Debits AT' == $order->getPayment()->getAdditionalInformation('PM')
        ) {
            if ('authorize_capture' == Mage::getModel('ops/config')->getPaymentAction()) {
                return 'VEN';
            }
            return 'RES';
        }
        // no RES for Direct Debits NL, so we'll do the final sale
        if ('Direct Debits NL' == $order->getPayment()->getAdditionalInformation('PM')) {
            return 'VEN';
        }

        if ('authorize_capture' == Mage::getModel('ops/config')->getPaymentAction()) {
            $operation = 'SAL';
        }

        return $operation;
    }
}
