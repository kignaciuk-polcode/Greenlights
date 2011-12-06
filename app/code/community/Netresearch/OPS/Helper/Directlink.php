<?php
/**
 * Netresearch_OPS_Helper_DirectLink
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_DirectLink extends Mage_Core_Helper_Abstract
{
    /**
     * Creates Transactions for directlink activities
     *
     * @param Mage_Sales_Model_Order $order
     * @param int $transactionID - persistent transaction id
     * @param int $subPayID - identifier for each transaction
     * @param array $arrInformation - add dynamic data
     * @param string $typename - name for the transaction exp.: refund
     * @param string $comment - order comment
     * 
     * @return Netresearch_OPS_Helper_DirectLink $this
     */
    public function directLinkTransact($order,$transactionID, $subPayID,
        $arrInformation = array(), $typename, $comment, $closed = 0)
    {
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionID."/".$subPayID);
        $transaction = $payment->addTransaction($typename, null, false, $comment);
        $transaction->setParentTxnId($transactionID);
        $transaction->setIsClosed($closed);
        $transaction->setAdditionalInformation("arrInfo", serialize($arrInformation));
        $transaction->save();
        $order->save();
        return $this;
    }

    /**
     * Checks if there is an active transaction for a special order for special
     * type
     *
     * @param string $type - refund, capture etc.
     * @param int $orderID
     * @return bol success
     */
    public function checkExistingTransact($type, $orderID)
    {
        $transaction = Mage::getModel('sales/order_payment_transaction')
            ->getCollection()
            ->addAttributeToFilter('order_id', $orderID)
            ->addAttributeToFilter('txn_type', $type)
            ->addAttributeToFilter('is_closed', 0)
            ->getLastItem();

        return ($transaction->getTxnId()) ? true : false;
    }

    /**
     * get transaction type for given OPS status
     *
     * @param string $status
     *
     * @return string
     */
    public function getTypeForStatus($status)
    {
        switch ($status) {
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUNDED :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_UNCERTAIN_STATUS :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_REFUSED :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_DECLINED_ACQUIRER :
                return Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_TRANSACTION_TYPE;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSED_MERCHANT :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_IN_PROGRESS:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REFUSED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DECLINED_ACQUIRER:
                return Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_TRANSACTION_TYPE;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOIDED: //Void finished
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOIDED_ACCEPTED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_UNCERTAIN:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_REFUSED:
                return Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_TRANSACTION_TYPE;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_UNCERTAIN:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_REFUSED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_OK:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_PROCESSED_MERCHANT:
                return Netresearch_OPS_Model_Payment_Abstract::OPS_DELETE_TRANSACTION_TYPE;
        }
    }

    /**
     * Process Direct Link Feedback to do: Capture, De-Capture and Refund
     * 
     * @param Mage_Sales_Model_Order $order  Order
     * @param array                  $params Request params
     *
     * @return void
     */
    public function processFeedback($order, $params)
    {
        Mage::helper('ops/payment')->saveOpsStatusToPayment($order->getPayment(), $params);
        try {
            $transaction = $this->getPaymentTransaction($order, null, $this->getTypeForStatus($params['STATUS']));
        } catch (Mage_Core_Exception $e) {
            $transaction = null;
        }
        
        if (false == $this->isValidOpsRequest($transaction, $order, $params)) {
            $order->addStatusHistoryComment(
                Mage::helper('ops')->__(
                    'Could not perform actions for Ogone status: %s.',
                    Mage::helper('ops')->getStatusText($params['STATUS'])
                )
            )->save();
            throw new Mage_Core_Exception('invalid Ogone request');
        }
        switch ($params['STATUS']) {
            case Netresearch_OPS_Model_Payment_Abstract::OPS_INVALID : 
                break;

            /*
             * Refund Actions
             */
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUNDED :
                Mage::helper('ops/order_refund')->createRefund($order, $params);
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_UNCERTAIN_STATUS :
                $order->addStatusHistoryComment(
                    Mage::helper('ops')->__(
                        'Refund is waiting or uncertain. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($params['STATUS'])
                    )
                ); 
                $order->save();
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_REFUSED :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_DECLINED_ACQUIRER :
                $this->closePaymentTransaction(
                    $order, 
                    $params, 
                    Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_TRANSACTION_TYPE,
                    Mage::helper('ops')->__(
                        'Refund was refused. Automatic creation failed. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($params['STATUS'])
                    )
                );
                break;

            /*
             * Capture Actions
             */
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED :
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSED_MERCHANT :
                Mage::helper("ops/order_capture")->acceptCapture($params);
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_IN_PROGRESS:
                $order->addStatusHistoryComment(
                    Mage::helper('ops')->__(
                        'Capture is waiting or uncertain. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($params['STATUS'])
                    )
                );
                $order->save();
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REFUSED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DECLINED_ACQUIRER:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTH_REFUSED : 
                $this->closePaymentTransaction(
                    $order,
                    $params,
                    Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_TRANSACTION_TYPE,
                    Mage::helper('ops')->__(
                        'Capture was refused. Automatic creation failed. Ogone status: %s.',
                        $params['STATUS']
                    )
                );
                break;

            /*
             * Void Actions
             */
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOIDED: //Void finished
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOIDED_ACCEPTED:
                Mage::helper("ops/order_void")->acceptVoid($params);
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_UNCERTAIN:
                $order->addStatusHistoryComment(
                    Mage::helper('ops')->__(
                        'Void is waiting or uncertain. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($params['STATUS'])
                    )
                );
                $order->save();
                break;
            case Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_REFUSED:
                $this->closePaymentTransaction(
                    $order,
                    $params,
                    Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_TRANSACTION_TYPE,
                    Mage::helper('ops')->__(
                        'Void was refused. Automatic creation failed. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($params['STATUS'])
                    )
                );
                break;
                
            /*
             * Authorize Actions
             */
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN:
            case Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_TO_GET_MANUALLY:
                $order->addStatusHistoryComment(Mage::helper('ops')->__('Authorization status changed. Current Ogone status is: %s.', Mage::helper('ops')->getStatusText($params['STATUS'])));
                $order->save();
                break;
                
            default:
                $order->addStatusHistoryComment(
                    Mage::helper('ops')->__('Unknown Ogone status: %s.', Mage::helper('ops')->getStatusText($params['STATUS']))
                );
                $order->save();
                Mage::helper("ops")->log("Unknown status code:".$params['STATUS']);
                break;
        }
    }

    /**
     * Get the payment transaction by PAYID and Operation
     * 
     * @param Mage_Sales_Model_Order $order 
     * @param int                    $payid
     * @param string                 $authorization
     *
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function getPaymentTransaction($order, $payid, $operation)
    {
        $helper = Mage::helper('ops');
        $transactionCollection = Mage::getModel('sales/order_payment_transaction')
            ->getCollection()
            ->addAttributeToFilter('txn_type', $operation)
            ->addAttributeToFilter('is_closed', 0)
            ->addAttributeToFilter('order_id', $order->getId());
        if ($payid != '') {
            $transactionCollection->addAttributeToFilter('parent_txn_id', $payid);
        }

        if ($transactionCollection->count()>1 || $transactionCollection->count() == 0) {
            $errorMsq = $helper->__(
                'Error, transaction count is %s instead of 1 for the Payid "%s", order "%s" and Operation "%s".',
                $transactionCollection->count(),
                $payid,
                $order->getId(),
                $operation
            );
            $helper->log($errorMsq);
            throw new Mage_Core_Exception($errorMsq);
        }

        if ($transactionCollection->count() == 1) {
            $transaction = $transactionCollection->getLastItem();
            $transaction->setOrderPaymentObject($order->getPayment());
            return $transaction;
        }
    }


    /**
     * Check if there are payment transactions for an order and an operation
     * 
     * @param Mage_Sales_Model_Order $order 
     * @param string $authorization
     *
     * @return boolean
     */
    public function hasPaymentTransactions($order, $operation)
    {
        $helper = Mage::helper('ops');
        $transactionCollection = Mage::getModel('sales/order_payment_transaction')
            ->getCollection()
            ->addAttributeToFilter('txn_type', $operation)
            ->addAttributeToFilter('is_closed', 0)
            ->addAttributeToFilter('order_id', $order->getId());

        return (0 < $transactionCollection->count());
    }

    /**
     * determine if the current OPS request is valid
     * 
     * @param array                  $transactions     Iteratable of Mage_Sales_Model_Order_Payment_Transaction 
     * @param Mage_Sales_Model_Order $order
     * @param array                  $opsRequestParams 
     *
     * @return boolean
     */
    public function isValidOpsRequest($openTransaction, Mage_Sales_Model_Order $order, $opsRequestParams)
    {
        if ($this->getTypeForStatus($opsRequestParams['STATUS']) == Netresearch_OPS_Model_Payment_Abstract::OPS_DELETE_TRANSACTION_TYPE) {
            return false;
        }

        $requestedAmount = null;
        if (array_key_exists('amount', $opsRequestParams)) {
            $requestedAmount = $opsRequestParams['amount'];
        }

        /* find expected amount */
        $expectedAmount = null;
        if (false === is_null($openTransaction)) {
            $transactionInfo = unserialize($openTransaction->getAdditionalInformation('arrInfo'));
            if (array_key_exists('amount', $transactionInfo)) {
                if (
                    is_null($expectedAmount)
                    || $transactionInfo['amount'] == $requestedAmount
                ) {
                    $expectedAmount = $transactionInfo['amount'];
                }
            }
        }

        if ($this->getTypeForStatus($opsRequestParams['STATUS']) == Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_TRANSACTION_TYPE
            || $this->getTypeForStatus($opsRequestParams['STATUS']) == Netresearch_OPS_Model_Payment_Abstract::OPS_VOID_TRANSACTION_TYPE
        ) {
            if (is_null($requestedAmount)
                || 0 == count($openTransaction)
                || $requestedAmount != $expectedAmount
            ) {
                return false;
            }
        }

        if ($this->getTypeForStatus($opsRequestParams['STATUS']) == Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_TRANSACTION_TYPE) {
            if (is_null($requestedAmount)) {
                Mage::helper('ops')->log('Please configure Ogone to submit amount');
                return false;
            }
            if ($order->getGrandTotal() != $requestedAmount) {
                if (is_null($openTransaction)
                    || $expectedAmount != $requestedAmount
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Close a payment transaction
     * 
     * @param Mage_Sales_Model_Order $order 
     * @param array $params
     *
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function closePaymentTransaction($order, $params, $type, $comment = "", $isCustomerNotified = false)
    {
        $transaction = Mage::helper('ops/directlink')->getPaymentTransaction(
            $order,
            $params['PAYID'], 
            $type
        );

        if (1 !== $transaction->getIsClosed()) {
            $transaction->setIsClosed(1);
            $transaction->save();
        }

        $trandactionID = $transaction->getTxnId();
        if ($comment) {
            $comment .= ' Transaction ID: '.'"'.$trandactionID.'"';
            $order
               ->addStatusHistoryComment($comment)
               ->setIsCustomerNotified($isCustomerNotified);
        }
        $order->save();
    }
}
