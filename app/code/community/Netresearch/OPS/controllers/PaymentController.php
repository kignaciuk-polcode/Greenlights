<?php
/**
 * Netresearch_OPS_PaymentController
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_PaymentController extends Netresearch_OPS_Controller_Abstract
{
    /**
     * Load place from layout to make POST on ops
     */
    public function placeformAction()
    {
        $lastIncrementId = $this->_getCheckout()->getLastRealOrderId();

        if ($lastIncrementId) {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($lastIncrementId);

            // update transactions, order state and add comments
            $order->getPayment()->setTransactionId($order->getQuoteId());
            $order->getPayment()->setIsTransactionClosed(false);
            $transaction = $order->getPayment()->addTransaction("authorization", null, true, $this->__("Process outgoing transaction"));
   
            if ($order->getId()) {
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage::helper('ops')->__('Start Ogone processing')
                );
                $order->save();
            }
        }

        $this->_getCheckout()->getQuote()->setIsActive(false)->save();
        $this->_getCheckout()->setOPSQuoteId($this->_getCheckout()->getQuoteId());
        $this->_getCheckout()->setOPSLastSuccessQuoteId($this->_getCheckout()->getLastSuccessQuoteId());
        $this->_getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Render 3DSecure response HTML_ANSWER
     */
    public function placeform3dsecureAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display our pay page, need to ops payment with external pay page mode     *
     */
    public function paypageAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * when payment gateway accept the payment, it will land to here
     * need to change order status as processed ops
     * update transaction id
     *
     */
    public function acceptAction()
    {
        if ($this->isJsonRequested($this->getRequest()->getParams())) {
            $result = array('result' => 'success', 'alias' => $this->_request->getParam('Alias'));
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }

        try {
            $this->checkRequestValidity();
            $this->getPaymentHelper()->applyStateForOrder(
                $this->_getOrder(),
                $this->getRequest()->getParams()
            );
        } catch (Exception $e) {
            $helper = Mage::helper('ops');
            $helper->log($helper->__("Exception in acceptAction: ".$e->getMessage()));
            $this->getPaymentHelper()->refillCart($this->_getOrder());
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_redirect('checkout/onepage/success');
    }

    /**
     * the payment result is uncertain
     * exception status can be 52 or 92
     * need to change order status as processing ops
     * update transaction id
     *
     */
    public function exceptionAction()
    {
        $params = $this->getRequest()->getParams();

        if ($this->isJsonRequested($params)) {
            Mage::log($params);
            $errors = array();

            foreach ($params as $key => $value) {
                if (stristr($key, 'error') && 0 != $value) {
                    $errors[] = $value;
                }
            }

            $result = array('result' => 'failure', 'errors' => $errors);
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }

        try {
            $this->checkRequestValidity();
            $this->getPaymentHelper()->handleException(
                $this->_getOrder(),
                $this->getRequest()->getParams()
            );
        } catch (Exception $e) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_redirect('checkout/onepage/success');
    }

    /**
     * when payment got decline
     * need to change order status to cancelled
     * take the user back to shopping cart
     *
     */
    public function declineAction()
    {
        try {
            $this->checkRequestValidity();
            $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOPSQuoteId());
            $this->getPaymentHelper()->declineOrder(
                $this->_getOrder(),
                $this->getRequest()->getParams()
            );
        } catch (Exception $e) { }

        $this->getPaymentHelper()->refillCart($this->_getOrder());

        $message = Mage::helper('ops')->__('Your payment information was declined. Please select another payment method.');
        Mage::getSingleton('core/session')->addNotice($message);

        $this->_redirect('checkout/onepage');
    }

    /**
     * when user cancel the payment
     * change order status to cancelled
     * need to redirect user to shopping cart
     *
     * @return Netresearch_OPS_ApiController
     */
    public function cancelAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            $this->checkRequestValidity();
            $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOPSQuoteId());
            $this->getPaymentHelper()->cancelOrder(
                $this->_getOrder(),
                $params,
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage::helper('ops')->__(
                    'Order canceled on Ogone side. Status: %s, Payment ID: %s.',
                    Mage::helper('ops')->getStatusText($params['STATUS']),
                    $params['PAYID'])
            );
        } catch (Exception $e) { }
        
        $this->getPaymentHelper()->refillCart($this->_getOrder());        
        $this->_redirect('checkout/cart');
    }
    
    /**
     * when user cancel the payment and press on button "Back to Catalog" or "Back to Merchant Shop" in Orops
     *
     * @return Netresearch_OPS_ApiController
     */
    public function continueAction()
    {
        $order = Mage::getModel('sales/order')->load(
            $this->_getCheckout()->getLastOrderId()
        );
        $this->getPaymentHelper()->refillCart($order);
        $redirect = $this->getRequest()->getParam('redirect');
        if ($redirect == 'catalog'): //In Case of "Back to Catalog" Button in OPS
            $this->_redirect('/'); 
        else: //In Case of Cancel Auto-Redirect or "Back to Merchant Shop" Button
            $this->_redirect('checkout/cart'); 
        endif;
    }
    
    /*
     * Check the validation of the request from OPS
     */
    protected function checkRequestValidity()
    {
        if (!$this->_validateOPSData()) {
            throw new Exception("Hash is not valid");
        }
    }

    /**
     * Return json encoded hash
     */
    public function generateHashAction()
    {
        $config = Mage::getModel('ops/config');

        $data = array(
            'ACCEPTURL'     => $config->getAcceptUrl(),
            'BRAND'         => $this->_request->getParam('brand'),
            'EXCEPTIONURL'  => $config->getExceptionUrl(),
            'ORDERID'       => $this->_request->getParam('orderid'),
            'PARAMPLUS'     => $this->_request->getParam('paramplus'),
            'PSPID'         => $config->getPSPID(),
        );

        $secret = $config->getShaOutCode();
        $raw = null;
        foreach ($data as $key => $value) {
            $raw .= sprintf('%s=%s%s', $key, $value, $secret);
        }

        $result = array('hash' => Mage::helper('ops/payment')->shaCrypt($raw));
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function registerDirectDebitPaymentAction()
    {
        $payment = $this->_getCheckout()->getQuote()->getPayment();
        $accountHolder = $this->_request->getParam('CN');
        $country = $this->_request->getParam('country');
        $account = $this->_request->getParam('account');
        $bankCode = $this->_request->getParam('bankcode');
        if (!is_numeric($account)) {
            $this->getResponse()
                ->setHttpResponseCode(406)
                ->setBody($this->__('Account number must contain numbers only.'))
                ->sendHeaders();
            return;
        }

        $payment->setAdditionalInformation('PM', 'Direct Debits ' . $country);

        if ('DE' == $country || 'AT' == $country) {
            $payment->setAdditionalInformation('CARDNO', $account . 'BLZ' . $bankCode);

            if (!is_numeric($bankCode)) {
                $this->getResponse()
                    ->setHttpResponseCode(406)
                    ->setBody($this->__('Bank code must contain numbers only.'))
                    ->sendHeaders();
                return;
            }
        }
        if ('NL' == $country) {
            if (strlen($accountHolder) < 1) {
                $this->getResponse()
                    ->setHttpResponseCode(406)
                    ->setBody($this->__('Account Holder must be filled in.'))
                    ->sendHeaders();
                return;
            }
            $payment->setAdditionalInformation('CARDNO', str_pad($account, '0', STR_PAD_LEFT));
        }

        $payment->setAdditionalInformation('CN', $accountHolder);
        $payment->save();

        $this->getResponse()->sendHeaders();
    }

    public function saveAliasAction()
    {
        $alias = $this->_request->getParam('alias');
        // Mage::log('Ogone PaymentController saveAliasAction', null, 'ogone_alias.log');
        if (0 < strlen($alias)) {
            $payment = $this->_getCheckout()->getQuote()->getPayment();
            $payment->setAdditionalInformation('alias', $alias);
            $payment->setDataChanges(true);
            $payment->save();
            // Mage::log($this->_request->getParams(), null, 'ogone_alias.log');
            // Mage::log($this->_getCheckout()->getQuote()->getPayment()->getAdditionalInformation(), null, 'ogone_alias.log');
            Mage::helper('ops')->log('saved alias ' . $alias . ' for quote #' . $this->_getCheckout()->getQuote()->getId());
        } else {
            Mage::log('did not save alias due to empty alias:', null, 'ogone_alias.log');
            Mage::log($this->_request->getParams(), null, 'ogone_alias.log');
        }
    }

    public function saveCcBrandAction()
    {
        $brand = $this->_request->getParam('brand');
        $cn = $this->_request->getParam('cn');

        $payment = $this->_getCheckout()->getQuote()->getPayment();
        $payment->setAdditionalInformation('CC_BRAND', $brand);
        $payment->setAdditionalInformation('CC_CN', $cn);
        $payment->setDataChanges(true);
        $payment->save();
        Mage::helper('ops')->log('saved cc brand ' . $brand . ' for quote #' . $this->_getCheckout()->getQuote()->getId());
        $this->getResponse()->sendHeaders();
    }
}
