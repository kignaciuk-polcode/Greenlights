<?php
/**
 * Netresearch_OPS_Helper_Order_Capture
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_Order_Capture extends Mage_Core_Helper_Abstract
{
    /**
     * Checks if partial capture
     *
     * @param Mage_Sales_Order_Payment $payment
     * @param float $amount
     * @return bool
    */
    public function determineIsPartial($payment, $amount)
    {
        $grandTotal = $payment->getOrder()->getGrandTotal();
        if ($grandTotal != $amount) {
            return true;
        } else {
            return false;
        }
    }

    public function prepareOperation($payment, $amount)
    {
        $isPartial = $this->determineIsPartial($payment, $amount);

        $params = Mage::app()->getRequest()->getParams();
        $arrInfo = $params['invoice'];
        $arrInfo['amount'] = $amount;

        if ($isPartial) {
            $arrInfo['items'] = $params['invoice']['items'];
            $arrInfo['operation'] = Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_PARTIAL;
            $arrInfo['type'] = "partial";
        } else {
            $arrInfo['operation'] = Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_FULL;
            $arrInfo['type'] = "full";
        }

        return $arrInfo;
    }
    
    /**
     * Creates the Invoice for the appropriate capture
     *
     *
     * @param array $params
     */
    public function acceptCapture($params)
    {
        Mage::register('ops_auto_capture', true);
        
        $orderID = $params['orderID'];
        $payID = $params['PAYID'];
        $order = Mage::getModel("sales/order")->loadByAttribute('quote_id', $params['orderID']);
        if ($payID) {
            $transaction = Mage::helper("ops/directlink")->getPaymentTransaction(
                $order,
                $payID,
                Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_TRANSACTION_TYPE
            );
            if ($transaction) {
               $arrInfoSerialized = $transaction->getAdditionalInformation();
               $arrInfo = unserialize($arrInfoSerialized['arrInfo']);
            }
        }

        if ($arrInfo['type'] == "full") {
            if (!$order->getInvoiceCollection()->getSize()) {
                $invoice = $order->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                $comment = Mage::helper("ops")->__("Capture process complete");
            } else {
                Mage::throwException(
                    Mage::helper('ops')->__('The capture has already been invoiced.')
                );
            }
        } else {
            $invoice = Mage::getModel('sales/service_order', $order)
                ->prepareInvoice($arrInfo['items']);
            if (!$invoice->getTotalQty()) {
                Mage::throwException(
                    Mage::helper('ops')->__('Cannot create an invoice without products.')
                );
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $comment = Mage::helper("ops")->__("Partial capture process complete");
        }
        
        if (is_object($invoice)) {
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());
            $shipment = false;
            if (isset($transaction)
                && array_key_exists('do_shipment', $arrInfo)
                && $arrInfo['do_shipment']
            ) {
                $shipment = $this->_prepareShipment($invoice, $arrInfo);
                if ($shipment) {
                    $shipment->setEmailSent($invoice->getEmailSent());
                    $transactionSave->addObject($shipment);
                }
            }
            
            $transactionSave->save();

            //Send E-Mail and Comment
            $sendEMail = false;
            $sendEMailWithComment = false;
            if (isset($arrInfo['send_email'])) $sendEMail = true;
            if (isset($arrInfo['comment_customer_notify'])) $sendEMailWithComment = true;
            $comment = array_key_exists('comment_text', $arrInfo) ? $arrInfo['comment_text'] : '';

            $invoice->addComment($comment,$sendEMailWithComment);
            if ($sendEMail) {
                $invoice->sendEmail(true, $comment);
                $invoice->setEmailSent(true);
            }
            $invoice->save();

            Mage::helper("ops/directlink")->closePaymentTransaction(
                $order,
                $params,
                Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_TRANSACTION_TYPE,
                Mage::helper('ops')->__(
                    'Invoice "%s" was created automatically. Ogone Status: %s.',
                    $invoice->getIncrementId(),
                    Mage::helper('ops')->getStatusText($params['STATUS'])
                ),
                $sendEMail
            );
            Mage::helper('ops')->log("Partial invoice created for order: %s", $orderID);
        }
    }

    /**
     * Prepare shipment
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice        New invoice
     * @param array                          $additionalData Array containing additional transaction data
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _prepareShipment($invoice, $additionalData)
    {
        $savedQtys = $additionalData['items'];
        $shipment = Mage::getModel('sales/service_order', $invoice->getOrder())
            ->prepareShipment($savedQtys);
        if (!$shipment->getTotalQty()) {
            return false;
        }

        $shipment->register();
        if (array_key_exists('tracking', $additionalData)
            && $additionalData['tracking']
        ) {
            foreach ($additionalData['tracking'] as $data) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->addData($data);
                $shipment->addTrack($track);
            }
        }
        return $shipment;
    }
}
