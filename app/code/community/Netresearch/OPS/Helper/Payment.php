<?php
/**
 * Netresearch_OPS_Helper_Payment
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_Payment extends Mage_Core_Helper_Abstract
{
    const HASH_ALGO = 'sha1';

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
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getConfig()
    {
        return Mage::getSingleton('ops/config');
    }

    /**
     * Crypt Data by SHA1 ctypting algorithm by secret key
     *
     * @param array $data
     * @param string $key
     * @return hash
     */
    public function shaCrypt($data, $key = '')
    {
        if (is_array($data)) {
            return hash(self::HASH_ALGO, implode("", $data));
        }if (is_string($data)) {
            return hash(self::HASH_ALGO, $data);
        } else {
            return "";
        }
    }

    /**
     * Check hash crypted by SHA1 with existing data
     *
     * @param array $data
     * @param string $hash
     * @param string $key
     * @return bool
     */
    public function shaCryptValidation($data, $hash, $key='')
    {
        if (is_array($data)) {
            return (bool)(strtoupper(hash(self::HASH_ALGO, implode("", $data)))== $hash);
        } elseif (is_string($data)) {
            return (bool)(strtoupper(hash(self::HASH_ALGO, $data)) == $hash);
        } else {
            return false;
        }
    }

    /**
     * Return set of data which is ready for SHA crypt
     *
     * @param array $data
     * @param string $key
     *
     * @return string
     */
    public function getSHAInSet($params, $SHAkey)
    {
        $params = $this->prepareParamsAndSort($params);
        $plainHashString = "";
        foreach ($params as $paramSet):
            if ($paramSet['value'] == '' || $paramSet['key'] == 'SHASIGN') continue;
            $plainHashString .= strtoupper($paramSet['key'])."=".$paramSet['value'].$SHAkey;
        endforeach;
        return $plainHashString;
    }
    
    /**
     * Return prepared and sorted array for SHA Signature Validation
     *
     * @param array $params
     *
     * @return string
     */
    protected function prepareParamsAndSort($params)
    {
        unset($params['CardNo']);
        unset($params['Brand']);
        unset($params['SHASign']);
        $params = array_change_key_case($params,CASE_UPPER);
        
        //PHP ksort take care about "_", Ogone not
        $sortedParams = array();
        foreach ($params as $key => $value):
            $sortedParams[str_replace("_", "", $key)] = array('key' => $key, 'value' => $value);
        endforeach;
        ksort($sortedParams);
        return $sortedParams;
    }
    
    /*
     * Get SHA-1-IN hash for ops-authentification 
     * 
     * All Parameters have to be alphabetically, UPPERCASE
     * Empty Parameters shoudln't appear in the secure String
     *
     * @param array $formFields
     * @param string $shaCode
     * 
     * @return string
     */
    public function getSHASign($formFields, $shaCode = null)
    {
        if (is_null($shaCode)) $shaCode = Mage::getModel('ops/config')->getShaOutCode();
        $formFields = array_change_key_case($formFields,CASE_UPPER);
        ksort($formFields);
        $plainHashString = "";
        foreach ($formFields as $formKey => $formVal):
            if (empty($formVal) || $formKey == 'SHASIGN') continue;
            $plainHashString .= strtoupper($formKey)."=".$formVal.$shaCode;
        endforeach;
        
        return $plainHashString;
    }

    /**
     * We get some CC info from ops, so we must save it
     *
     * @param Mage_Sales_Model_Order $order
     * @param array $ccInfo
     *
     * @return Netresearch_OPS_ApiController
     */
    public function _prepareCCInfo($order, $ccInfo)
    {
        if(isset($ccInfo['CN'])){
            $order->getPayment()->setCcOwner($ccInfo['CN']);
        }
        
        if(isset($ccInfo['CARDNO'])){
            $order->getPayment()->setCcNumberEnc($ccInfo['CARDNO']);
            $order->getPayment()->setCcLast4(substr($ccInfo['CARDNO'], -4));
        }

        if(isset($ccInfo['ED'])){
            $order->getPayment()->setCcExpMonth(substr($ccInfo['ED'], 0, 2));
            $order->getPayment()->setCcExpYear(substr($ccInfo['ED'], 2, 2));
        }

        return $this;
    }

    public function isPaymentAccepted($status)
    {
        return in_array($status, array(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED,
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING,
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN,
            Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT,
            Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED,
            Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING,
            Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN,
            Netresearch_OPS_Model_Payment_Abstract::OPS_WAITING_FOR_IDENTIFICATION
        ));
    }
    
    public function isPaymentAuthorizeType($status)
    {
        return in_array($status, array(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED,
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING,
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN,
            Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT
        ));
    }
    
    public function isPaymentCaptureType($status)
    {
        return in_array($status, array(
            Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED,
            Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING,
            Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN
        ));
    }

    public function isPaymentFailed($status)
    {
        return false == $this->isPaymentAccepted($status);
    }

    /**
     * apply ops state for order
     * 
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     *
     * @return void
     */
    public function applyStateForOrder($order, $params)
    {
        switch ($params['STATUS']) {
            case Netresearch_OPS_Model_Payment_Abstract::OPS_INVALID : 
                break;
                
            case Netresearch_OPS_Model_Payment_Abstract::OPS_WAITING_FOR_IDENTIFICATION : //3D-Secure
                $this->waitOrder($order, $params);
                break;
                
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT:
                $this->acceptOrder($order, $params);
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN:
                $this->acceptOrder($order, $params, 1);
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTH_REFUSED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_CANCELED_BY_CUSTOMER:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REFUSED:
                $this->declineOrder($order, $params);
                break;
            default:
                //all unknown transaction will accept as exceptional
                $this->handleException($order, $params);
        }
    }

    /**
     * Process success action by accept url
     *
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     */
    public function acceptOrder($order, $params, $instantCapture = 0)
    {
        $this->_getCheckout()->setLastSuccessQuoteId($order->getQuoteId());
        $this->_prepareCCInfo($order, $params);
        $this->setPaymentTransactionInformation($order->getPayment(),$params);
        
        if ($transaction = Mage::helper('ops/payment')->getTransactionByTransactionId($order->getQuoteId())):
            $transaction->setTxnId($params['PAYID'])->save();
        endif;

        try {
            if (($this->getConfig()->getConfigData('payment_action') == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE 
                || $instantCapture)
                && $params['STATUS'] != Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT) {
                $this->_processDirectSale($order, $params, $instantCapture);
            } else {
                $this->_processAuthorize($order, $params);
            }
        } catch (Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ops')->__('Order can not be saved.'));
            throw $e;
        }
    }
    
    /**
     * Set Payment Transaction Information
     *
     * @param Mage_Sales_Model_Order_Payment $payment Sales Payment Model
     * @param array                  $params Request params
     */
    protected function setPaymentTransactionInformation($payment,$params)
    {
        $payment->setTransactionId($params['PAYID']);
        $code = $payment->getMethodInstance()->getCode();

        if (in_array($code, array('ops_cc', 'ops_directDebit'))) {
            $payment->setIsTransactionClosed(false);
            $payment->addTransaction("authorization", null, true, $this->__("Process outgoing transaction"));
            $payment->setLastTransId($params['PAYID']);
            if (isset($params['HTML_ANSWER'])) $payment->setAdditionalInformation('HTML_ANSWER', $params['HTML_ANSWER']);
        }

        $payment->setAdditionalInformation('paymentId', $params['PAYID']);
        $payment->setAdditionalInformation('status', $params['STATUS']);
        $payment->setIsTransactionClosed(true);
        $payment->setDataChanges(true);
        $payment->save();
    }

    /**
     * Process cancel action by cancel url
     *
     * @param Mage_Sales_Model_Order $order   Order
     * @param array                  $params  Request params
     * @param string                 $status  Order status
     * @param string                 $comment Order comment
     */
    public function cancelOrder($order, $params, $status, $comment)
    {
        try{
            Mage::register('ops_auto_void', true); //Set this session value to true to allow cancel
            $order->cancel();
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, $status, $comment);
            $order->save();            
            $this->setPaymentTransactionInformation($order->getPayment(),$params);
        } catch(Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ops')->__('Order can not be canceled for system reason.'));
            throw $e;
        }
    }

    /**
     * Process decline action by ops decline url
     *
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     */
    public function declineOrder($order, $params)
    {
        try{
            Mage::register('ops_auto_void', true); //Set this session value to true to allow cancel
            $order->cancel();
            $order->setState(
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage::helper('ops')->__(
                    'Order declined on ops side. Ogone status: %s, Payment ID: %s.',
                    Mage::helper('ops')->getStatusText($params['STATUS']),
                    $params['PAYID']
                )
            );
            $order->save();
            $this->setPaymentTransactionInformation($order->getPayment(),$params);
        } catch(Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ops')->__('Order can not be canceled for system reason.'));
            throw $e;
        }
    }
    
    /**
     * Process decline action by ops decline url
     *
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     */
    public function waitOrder($order, $params)
    {
        try {
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('ops')->__(
                    'Order is waiting for ops confirmation of 3D-Secure. Ogone status: %s, Payment ID: %s.',
                    Mage::helper('ops')->getStatusText($params['STATUS']),
                    $params['PAYID']
                )
            );
            $order->save();
            $this->setPaymentTransactionInformation($order->getPayment(), $params);
        } catch(Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ops')->__('Error during 3D-Secure processing of Ogone. Error: %s', $e->getMessage()));
            throw $e;
        }
    }

    /**
     * Process exception action by ops exception url
     *
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     */
    public function handleException($order, $params)
    {
        $exceptionMessage = $this->getPaymentExceptionMessage($params['STATUS']);

        if (!empty($exceptionMessage)) {
            try{
                $this->_getCheckout()->setLastSuccessQuoteId($order->getQuoteId());
                $this->_prepareCCInfo($order, $params);
                $order->getPayment()->setLastTransId($params['PAYID']);
                //to send new order email only when state is pending payment
                if ($order->getState()==Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                    $order->sendNewOrderEmail();
                }
                $order->addStatusHistoryComment($exceptionMessage);
                $order->save();
                $this->setPaymentTransactionInformation($order->getPayment(),$params);
            } catch(Exception $e) {
                $this->_getCheckout()->addError(Mage::helper('ops')->__('Order can not be saved for system reason.'));
            }
        } else {
            $this->_getCheckout()->addError(Mage::helper('ops')->__('An unknown exception occured.'));
        }
    }
    
    /**
     * Get Payment Exception Message
     *
     * @param int $ops_status Request OPS Status
     */
    protected function getPaymentExceptionMessage($ops_status)
    {
        $exceptionMessage = '';
        switch($ops_status) {
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN :
                $exceptionMessage = Mage::helper('ops')->__(
                    'A technical problem arose during payment process, giving unpredictable result. Ogone status: %s.',
                    Mage::helper('ops')->getStatusText($ops_status)
                );
                break;
            default:
                $exceptionMessage = Mage::helper('ops')->__(
                    'An unknown exception was thrown in the payment process. Ogone status: %s.',
                    Mage::helper('ops')->getStatusText($ops_status)
                );
        }
        return $exceptionMessage;
    }

    /**
     * Process Configured Payment Action: Direct Sale, create invoce if state is Pending
     *
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     */
    protected function _processDirectSale($order, $params, $instantCapture = 0)
    {
        Mage::register('ops_auto_capture', true);
        $status = $params['STATUS'];
        if ($status == Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT) {
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('ops')->__('Waiting for the payment of the customer')
            );
            $order->save();
        } elseif ($status == Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING) {
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('ops')->__('Authorization waiting from Ogone')
            );
            $order->save();
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
            || $instantCapture
        ) {
            if ($status == Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED) {
                if ($order->getStatus() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                    $order->setState(
                        Mage_Sales_Model_Order::STATE_PROCESSING,
                        Mage_Sales_Model_Order::STATE_PROCESSING,
                        Mage::helper('ops')->__('Processed by Ogone')
                    );
                }
            } else {
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    Mage::helper('ops')->__('Processed by Ogone')
                );
            }

            if (!$order->getInvoiceCollection()->getSize()) {
                $invoice = $order->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                $invoice->register();
                $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
                $invoice->getOrder()->setIsInProcess(true);
                $invoice->save();

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
                $order->sendNewOrderEmail();
            }
        } else {
            $order->save();
        }
    }


    /**
     * Process Configured Payment Actions: Authorized, Default operation
     * just place order
     *
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     */
    protected function _processAuthorize($order, $params)
    {
        $status = $params['STATUS'];
        if ($status == Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT) {
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('ops')->__('Waiting for payment. Ogone status: %s.', Mage::helper('ops')->getStatusText($status))
            );
        } elseif ($status ==  Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING) {
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('ops')->__('Authorization waiting. Ogone status: %s.', Mage::helper('ops')->getStatusText($status))
            );
        } elseif ($status ==  Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING) {
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('ops')->__('Authorization uncertain. Ogone status: %s.', Mage::helper('ops')->getStatusText($status))
            );
        } else {
            //to send new order email only when state is pending payment
            if ($order->getState()==Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                $order->sendNewOrderEmail();
            }

            $payId = $params['PAYID'];
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage::helper('ops')->__('Processed by Ogone. Payment ID: %s. Ogone status: %s.', $payId, Mage::helper('ops')->getStatusText($status))
            );
        }
        $order->save();
    }

    /**
     * Fetches transaction with given transaction id
     *
     * @param string $txnId
     * @return mixed Mage_Sales_Model_Order_Payment_Transaction | boolean
     */
    public function getTransactionByTransactionId($transactionId)
    {
        if (!$transactionId) {
            return;
        }
        $transaction = Mage::getModel('sales/order_payment_transaction')
            ->getCollection()
            ->addAttributeToFilter('txn_id', $transactionId)
            ->getLastItem();
        if (is_null($transaction->getId())) return false;
        $transaction->getOrderPaymentObject();
        return $transaction;
    }

    /**
     * refill cart
     * 
     * @param Mage_Sales_Model_Order $order 
     *
     * @return void
     */
    public function refillCart($order)
    {
        // add items
        $cart = Mage::getSingleton('checkout/cart');
        foreach ($order->getItemsCollection() as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }
        $cart->save();

        // add coupon code
        $coupon = $order->getCouponCode();
        $session = Mage::getSingleton('checkout/session');
        if (false == is_null($coupon)) {
            $session->getQuote()->setCouponCode($coupon)->save();
        }
    }
    
    /**
     * Save OPS Status to Payment
     * 
     * @param Mage_Sales_Model_Order_Payment $payment 
     * @param array $params OPS-Response
     *
     * @return void
     */
    public function saveOpsStatusToPayment(Mage_Sales_Model_Order_Payment $payment, $params)
    {
        $payment
            ->setAdditionalInformation('status', $params['STATUS'])
            ->save();
    }
}
