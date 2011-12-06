<?php
/**
 * Netresearch_OPS_Helper_Order_Refund
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_Order_Refund extends Mage_Core_Helper_Abstract
{
    protected $payment;
    protected $amount;
    
    /**
     * @param Varien_Object $payment 
     */
    public function setPayment(Varien_Object $payment)
    {
        $this->payment = $payment;
        return $this;
    }
    
    /**
     * @param float $amount 
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
 
    /**
     * Return the refund operation type (RFS or RFD)
     * 
     * @param Varien_Object $payment 
     * @param float $amount 
     * @return 
     */
    public function getRefundOperation()
    {
        if ($this->payment->getAmountPaid() == ($this->payment->getBaseAmountRefundedOnline()+$this->amount)):
            $operation = Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL;
        else:
            $operation = Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_PARTIAL;
        endif;
        return $operation;
    }
    
    /**
     * Create a new payment transaction for the refund
     * 
     * @param array $response 
     * @return 
     */
    public function createRefundTransaction($response, $closed = 0)
    {
        $transactionParams = array(
            'creditmemo_request' => Mage::app()->getRequest()->getParams(),
            'response'     => $response,
            'amount'             => $this->amount
        );
     
        Mage::helper('ops/directlink')->directLinkTransact(
            Mage::getModel('sales/order')->load($this->payment->getOrder()->getId()),
            $response['PAYID'],
            $response['PAYIDSUB'],
            $transactionParams, 
            Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_TRANSACTION_TYPE, 
            Mage::helper('ops')->__('Start Ogone refund request'),
            $closed
        );
        
        $order = Mage::getModel('sales/order')->load($this->payment->getOrder()->getId());
        $order->addStatusHistoryComment(
            Mage::helper('ops')->__(
               'Creditmemo will be created automatically as soon as Ogone sends an acknowledgement. Ogone Status: %s.',
               Mage::helper('ops')->getStatusText($response['STATUS'])
            )
        );
        $order->save();
    }
    
    /**
     * Create a new refund
     * 
     * @param Mage_Sales_Model_Order $order 
     * @param array $params
     * @return 
     */
    public function createRefund($order, $params)
    {
        $refundTransaction = Mage::helper('ops/directlink')->getPaymentTransaction(
            $order,
            $params['PAYID'], 
            Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_TRANSACTION_TYPE
        );
        $transactionParams = $refundTransaction->getAdditionalInformation();
        $transactionParams = unserialize($transactionParams['arrInfo']);
        
        try {
            //Start to create the creditmemo
            Mage::register('ops_auto_creditmemo', true);
            $service = Mage::getModel('sales/service_order', $order);
            $invoice = Mage::getModel('sales/order_invoice')
                    ->load($transactionParams['creditmemo_request']['invoice_id'])
                    ->setOrder($order);
            $data = $this->prepareCreditMemoData($transactionParams);
            $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
            
            /**
              * Process back to stock flags
            */
            $backToStock = $data['backToStock'];
            foreach ($creditmemo->getAllItems() as $creditmemoItem) {
                    $orderItem = $creditmemoItem->getOrderItem();
                    $parentId = $orderItem->getParentItemId();
                    if (isset($backToStock[$orderItem->getId()])) {
                        $creditmemoItem->setBackToStock(true);
                    } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                        $creditmemoItem->setBackToStock(true);
                    } elseif (empty($savedData)) {
                        $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
                    } else {
                        $creditmemoItem->setBackToStock(false);
                    }
            }
            
            //Send E-Mail and Comment
            $comment = '';
            $sendEmail = false;
            $sendEMailWithComment = false;
            if (isset($data['send_email']) && $data['send_email'] == 1) $sendEmail = true;
            if (isset($data['comment_customer_notify'])) $sendEMailWithComment = true;
            
            if (!empty($data['comment_text'])):
                    $creditmemo->addComment($data['comment_text'], $sendEMailWithComment);
                    if ($sendEMailWithComment):
                        $comment = $data['comment_text'];
                    endif;
            endif;

            
            $creditmemo->setRefundRequested(true);
            $creditmemo->setOfflineRequested(false);
            $creditmemo->register();
            if ($sendEmail):
                    $creditmemo->setEmailSent(true);
            endif;
            $creditmemo->getOrder()->setCustomerNoteNotify($sendEMailWithComment);
            
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($creditmemo)
                ->addObject($creditmemo->getOrder());
            if ($creditmemo->getInvoice()):
                $transactionSave->addObject($creditmemo->getInvoice());
            endif;
            $creditmemo->sendEmail($sendEmail, $comment);
            $transactionSave->save();
            //End of create creditmemo
            
            //close refund payment transaction
            Mage::helper('ops/directlink')->closePaymentTransaction(
               $order, 
               $params, 
               Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_TRANSACTION_TYPE, 
               Mage::helper('ops')->__(
                   'Creditmemo "%s" was created automatically. Ogone Status: %s.',
                   $creditmemo->getIncrementId(),
                   Mage::helper('ops')->getStatusText($params['STATUS'])
               ),
               $sendEmail
            );
        }
        catch (Exception $e) {
            Mage::throwException('Error in Creditmemo creation process: '.$e->getMessage());
        }
     
    }
    
    /**
     * Get requested items qtys
     */
    protected function prepareCreditMemoData($transactionParams)
    {
        $data = $transactionParams['creditmemo_request']['creditmemo'];
        $qtys = array();
        $backToStock = array();
        foreach ($data['items'] as $orderItemId =>$itemData):
           if (isset($itemData['qty'])):
               $qtys[$orderItemId] = $itemData['qty'];
           else:
               if (isset($itemData['back_to_stock'])):
                   $backToStock[$orderItemId] = true;
               endif;
           endif;
        endforeach;
        $data['qtys'] = $qtys;
        $data['backToStock'] = $backToStock;
        return $data;
    }
}
