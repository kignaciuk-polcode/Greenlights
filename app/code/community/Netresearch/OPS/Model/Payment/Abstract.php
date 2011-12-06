<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**OPS_PAYMENT_PROCESSING
 * OPS payment method model
 */
class Netresearch_OPS_Model_Payment_Abstract extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ops';
    protected $_formBlockType = 'ops/form';
    protected $_infoBlockType = 'ops/info';
    protected $_config = null;

     /**
     * Magento Payment Behaviour Settings
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = true;

    /** 
     * OPS template modes 
     */
    const TEMPLATE_OPS            = 'ops';
    const TEMPLATE_MAGENTO          = 'magento';

    /**
     * OPS response status 
     */
    const OPS_INVALID                             = 0;
    const OPS_PAYMENT_CANCELED_BY_CUSTOMER        = 1;
    const OPS_AUTH_REFUSED                        = 2;
    
    const OPS_ORDER_SAVED                         = 4;
    const OPS_AWAIT_CUSTOMER_PAYMENT              = 41;
    const OPS_WAITING_FOR_IDENTIFICATION          = 46;
    
    const OPS_AUTHORIZED                          = 5;
    const OPS_AUTHORIZED_WAITING                  = 51;
    const OPS_AUTHORIZED_UNKNOWN                  = 52;
    const OPS_STAND_BY                            = 55;
    const OPS_PAYMENTS_SCHEDULED                  = 56;
    const OPS_AUTHORIZED_TO_GET_MANUALLY          = 59;
    
    const OPS_VOIDED                              = 6;
    const OPS_VOID_WAITING                        = 61;
    const OPS_VOID_UNCERTAIN                      = 62;
    const OPS_VOID_REFUSED                        = 63;
    const OPS_VOIDED_ACCEPTED                     = 64;
    
    const OPS_PAYMENT_DELETED                     = 7;
    const OPS_PAYMENT_DELETED_WAITING             = 71;
    const OPS_PAYMENT_DELETED_UNCERTAIN           = 72;
    const OPS_PAYMENT_DELETED_REFUSED             = 73;
    const OPS_PAYMENT_DELETED_OK                  = 74;
    const OPS_PAYMENT_DELETED_PROCESSED_MERCHANT  = 75;
    
    const OPS_REFUNDED                            = 8;
    const OPS_REFUND_WAITING                      = 81;
    const OPS_REFUND_UNCERTAIN_STATUS             = 82;
    const OPS_REFUND_REFUSED                      = 83;
    const OPS_REFUND_DECLINED_ACQUIRER            = 84;
    const OPS_REFUND_PROCESSED_MERCHANT           = 85;
    
    const OPS_PAYMENT_REQUESTED                   = 9;
    const OPS_PAYMENT_PROCESSING                  = 91;
    const OPS_PAYMENT_UNCERTAIN                   = 92;
    const OPS_PAYMENT_REFUSED                     = 93;
    const OPS_PAYMENT_DECLINED_ACQUIRER           = 94;
    const OPS_PAYMENT_PROCESSED_MERCHANT          = 95;
    const OPS_PAYMENT_IN_PROGRESS                 = 99;

    /**
     * Layout of the payment method 
     */
    const PMLIST_HORIZONTAL_LEFT            = 0;
    const PMLIST_HORIZONTAL                 = 1;
    const PMLIST_VERTICAL                   = 2;

    /** 
     * OPS payment action constant
     */
    const OPS_AUTHORIZE_ACTION = 'RES';
    const OPS_AUTHORIZE_CAPTURE_ACTION = 'SAL';
    const OPS_CAPTURE_FULL = 'SAS';
    const OPS_CAPTURE_PARTIAL = 'SAL';
    const OPS_DELETE_AUTHORIZE = 'DEL';
    const OPS_DELETE_AUTHORIZE_AND_CLOSE = 'DES';
    const OPS_REFUND_FULL = 'RFS';
    const OPS_REFUND_PARTIAL = 'RFD';
    
    /**
     * 3D-Secure
     */
    const OPS_DIRECTLINK_WIN3DS = 'MAINW';
    
    /** 
     * Module Transaction Type Codes 
     */
    const OPS_CAPTURE_TRANSACTION_TYPE = 'capture';
    const OPS_VOID_TRANSACTION_TYPE = 'void';
    const OPS_REFUND_TRANSACTION_TYPE = 'refund';
    const OPS_DELETE_TRANSACTION_TYPE = 'delete';

    /**
     * Return OPS Config
     *
     * @return Netresearch_OPS_Model_Config
     */
    public function getConfig()
    {
        if (is_null($this->_config)):
           $this->_config = Mage::getSingleton('ops/config');
        endif;
        return $this->_config;
    }

    /**
     * Redirect url to ops submit form
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return $this->getConfig()->getPaymentRedirectUrl();
    }

    /**
     * Return payment_action value from config area
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getConfig()->getConfigData('payment_action');
    }

    /**
     * Rrepare params array to send it to gateway page via POST
     *
     * @param Mage_Sales_Model_Order
     * @return array
     */
    public function getFormFields($order)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }
        $billingAddress = $order->getBillingAddress();
        $formFields = array();
        $formFields['PSPID']    = $this->getConfig()->getPSPID();
        $formFields['ORDERID']  = $order->getQuoteId();
        $formFields['AMOUNT']   = round($order->getBaseGrandTotal()*100);
        $formFields['CURRENCY'] = Mage::app()->getStore()->getBaseCurrencyCode();
        $formFields['LANGUAGE'] = Mage::app()->getLocale()->getLocaleCode();
        $formFields['CN']       = $billingAddress->getFirstname().' '.$billingAddress->getLastname();
        $formFields['EMAIL']    = $order->getCustomerEmail();
        $formFields['OWNERZIP'] = $billingAddress->getPostcode();
        $formFields['OWNERCTY'] = $billingAddress->getCountry();
        $formFields['OWNERTOWN']= $billingAddress->getCity();        
        $formFields['COM']      = $this->_getOrderDescription($order);        
        $formFields['OWNERTELNO']   = $billingAddress->getTelephone();        
        $formFields['OWNERADDRESS'] =  str_replace("\n", ' ',$billingAddress->getStreet(-1));
        $formFields['ORIG'] = Mage::helper("ops")->getModuleVersionString();
        $formFields['PM'] = $this->getOpsCode();
        $formFields['BRAND'] = $this->getOpsBrand();

        $paymentAction = $this->_getOPSPaymentOperation();
        if ($paymentAction ) {
            $formFields['OPERATION'] = $paymentAction;
        }
        
        $formFields['HOMEURL']          = $this->getConfig()->getContinueUrl(array('redirect' => 'home'));
        $formFields['CATALOGURL']       = $this->getConfig()->getContinueUrl(array('redirect' => 'catalog'));
        $formFields['ACCEPTURL']        = $this->getConfig()->getAcceptUrl();
        $formFields['DECLINEURL']       = $this->getConfig()->getDeclineUrl();
        $formFields['EXCEPTIONURL']    = $this->getConfig()->getExceptionUrl();
        $formFields['CANCELURL']        = $this->getConfig()->getCancelUrl();

        if ($this->getConfig()->getConfigData('template')=='ops') {
            $formFields['TP']= '';
            $formFields['PMLISTYPE'] = $this->getConfig()->getConfigData('pmlist');
        } else {
            $formFields['TP']= $this->getConfig()->getPayPageTemplate();
        }
        $formFields['TITLE']            = $this->getConfig()->getConfigData('html_title');
        $formFields['BGCOLOR']          = $this->getConfig()->getConfigData('bgcolor');
        $formFields['TXTCOLOR']         = $this->getConfig()->getConfigData('txtcolor');
        $formFields['TBLBGCOLOR']       = $this->getConfig()->getConfigData('tblbgcolor');
        $formFields['TBLTXTCOLOR']      = $this->getConfig()->getConfigData('tbltxtcolor');
        $formFields['BUTTONBGCOLOR']    = $this->getConfig()->getConfigData('buttonbgcolor');
        $formFields['BUTTONTXTCOLOR']   = $this->getConfig()->getConfigData('buttontxtcolor');
        $formFields['FONTTYPE']         = $this->getConfig()->getConfigData('fonttype');
        $formFields['LOGO']             = $this->getConfig()->getConfigData('logo');        
        $formFields['SHASIGN']  = Mage::helper('ops/payment')->shaCrypt(Mage::helper('ops/payment')->getSHASign($formFields));
        
        $helper = Mage::helper('ops');
        $helper->log($helper->__("Register Order %s in Ogone \n\nAll form fields: %s\nOgone Plain Hash: %s\nHashed String: %s",
            $order->getIncrementId(),
            Zend_Json::encode($formFields),
            Mage::helper('ops/payment')->getSHASign($formFields),
            Mage::helper('ops/payment')->shaCrypt($formFields['SHASIGN'])
        ));
        
        return $formFields;
    }

    /**
     * Get OPS Payment Action value
     *
     * @param string
     * @return string
     */
    protected function _getOPSPaymentOperation()
    {
        $value = $this->getPaymentAction();
        if ($value==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            $value = self::OPS_AUTHORIZE_ACTION;
        } elseif ($value==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE) {
            $value = self::OPS_AUTHORIZE_CAPTURE_ACTION;
        }
        return $value;
    }

    /**
     * get formated order description
     *
     * @param Mage_Sales_Model_Order
     * @return string
     */
    protected function _getOrderDescription($order)
    {
        $invoiceDesc = '';
        $lengs = 0;
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) continue;
            //COM field is limited to 100 chars max
            if (Mage::helper('core/string')->strlen($invoiceDesc.$item->getName()) > 100) break;
            $invoiceDesc .= preg_replace("/[^a-zA-Z0-9äáéèíóöõúüûÄÁÉÍÓÖÕÚÜÛ_ ]/" , "" , $item->getName()) . ', ';
        }
        return Mage::helper('core/string')->substr($invoiceDesc, 0, -2);
    }

    /**
     * Get Main OPS Helper
     *
     * @return Netresearch_OPS_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('ops');
    }
    
    /**
     * Determines if a capture will be processed
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if (true === Mage::registry('ops_auto_capture')):
           Mage::unregister('ops_auto_capture');
           return parent::capture($payment, $amount);
        endif;

        $orderID = $payment->getOrder()->getId();
        $arrInfo = Mage::helper('ops/order_capture')->prepareOperation($payment, $amount);
        
        if(Mage::helper('ops/directlink')->checkExistingTransact(self::OPS_CAPTURE_TRANSACTION_TYPE,  $orderID)):
            $this->getHelper()->redirectNoticed($orderID, $this->getHelper()->__('You already sent a capture request. Please wait until the capture request is acknowledged.'));
        endif;
        if(Mage::helper('ops/directlink')->checkExistingTransact(self::OPS_VOID_TRANSACTION_TYPE,  $orderID)):
            $this->getHelper()->redirectNoticed($orderID, $this->getHelper()->__('There is one void request waiting. Please wait until this request is acknowledged.'));
        endif;
        
        try {
            $requestParams  = array(
                'AMOUNT' => $amount*100,
                'ORDERID' => $payment->getOrder()->getQuoteId(),
                'OPERATION' => $arrInfo['operation']
            );
            $response = Mage::getSingleton('ops/api_directlink')->performRequest($requestParams, Mage::getModel('ops/config')->getDirectLinkGatewayPath());
            Mage::helper('ops/payment')->saveOpsStatusToPayment($payment, $response);
            
            if ($response['STATUS'] == self::OPS_PAYMENT_PROCESSING ||
                $response['STATUS'] == self::OPS_PAYMENT_UNCERTAIN ||
                $response['STATUS'] == self::OPS_PAYMENT_IN_PROGRESS 
                ):
                Mage::helper('ops/directlink')->directLinkTransact(
                    Mage::getSingleton("sales/order")->loadByIncrementId($payment->getOrder()->getIncrementId()), 
                    $response['PAYID'], 
                    $response['PAYIDSUB'], 
                    $arrInfo, 
                    self::OPS_CAPTURE_TRANSACTION_TYPE, 
                    $this->getHelper()->__('Start Ogone %s capture request',$arrInfo['type']));
                $order = Mage::getModel('sales/order')->load($orderID); //Reload order to avoid wrong status
                $order->addStatusHistoryComment(
                    Mage::helper('ops')->__(
                        'Invoice will be created automatically as soon as Ogone sends an acknowledgement. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($response['STATUS'])
                    )
                );
                $order->save();
                $this->getHelper()->redirectNoticed(
                    $orderID,
                    $this->getHelper()->__(
                        'Invoice will be created automatically as soon as Ogone sends an acknowledgement. Ogone status: %s.',
                        Mage::helper('ops')->getStatusText($response['STATUS'])
                    )
                );
            elseif ($response['STATUS'] == self::OPS_PAYMENT_PROCESSED_MERCHANT || $response['STATUS'] == self::OPS_PAYMENT_REQUESTED):
                 return parent::capture($payment, $amount);
            else:
                 Mage::throwException(
                     $this->getHelper()->__(
                         'The Invoice was not created. Ogone status: %s.',
                         Mage::helper('ops')->getStatusText($response['STATUS'])
                     )
                 );
            endif;
        }
        catch (Exception $e){
            Mage::helper('ops')->log("Exception in capture request:".$e->getMessage());
            throw new Mage_Core_Exception($e->getMessage());
        }
    }

   
    
    /**
     * Refund
     * 
     * @param Varien_Object $payment 
     * @param float $amount 
     * @return 
     */
     public function refund(Varien_Object $payment, $amount)
     {
        //If the refund will be created by OPS, Refund Create Method to nothing
        if (true === Mage::registry('ops_auto_creditmemo')):
           Mage::unregister('ops_auto_creditmemo');
           return parent::refund($payment, $amount);
        endif;
        
        $refundHelper = Mage::helper('ops/order_refund');
        $refundHelper
           ->setPayment($payment)
           ->setAmount($amount);
        
        $operation = $refundHelper->getRefundOperation($payment, $amount);
        $requestParams  = array(
            'AMOUNT' => $amount*100,
            'ORDERID' => $payment->getOrder()->getQuoteId(),
            'OPERATION' => $operation
        );
        
        try {
            $url = Mage::getModel('ops/config')->getDirectLinkGatewayPath();
            $response = Mage::getModel('ops/api_directlink')->performRequest($requestParams, $url);
            Mage::helper('ops/payment')->saveOpsStatusToPayment($payment, $response);
            
            if ($response['STATUS'] == self::OPS_REFUND_WAITING || $response['STATUS'] == self::OPS_REFUND_UNCERTAIN_STATUS):
                $refundHelper->createRefundTransaction($response);
            elseif ($response['STATUS'] == self::OPS_REFUNDED || self::OPS_REFUND_PROCESSED_MERCHANT): //do refund directly if response is ok already
                $refundHelper->createRefundTransaction($response, 1);
                return parent::refund($payment, $amount);
            else:
                Mage::throwException($this->getHelper()->__('The CreditMemo was not created. Ogone status: %s.',$response['status']));
            endif;
            
            Mage::getSingleton('core/session')->addNotice($this->getHelper()->__('The Creditmemo will be created automatically as soon as Ogone sends an acknowledgement.'));
            $this->getHelper()->redirect(
                Mage::getUrl('*/sales_order/view', array('order_id' => $payment->getOrder()->getId()))
            );
        }
        catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }
    
    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        try
        {
            $order = Mage::getModel('sales/order')->load(Mage::app()->getRequest()->getParam('order_id'));
            if (false === Mage::helper('ops/directlink')->hasPaymentTransactions($order,self::OPS_REFUND_TRANSACTION_TYPE)):
                return $this->_canRefund;
            else:
                //Add the notice if no exception was thrown, because in this case there is one creditmemo in the transaction queue
                Mage::getSingleton('core/session')->addNotice(
                    $this->getHelper()->__('There is already one creditmemo in the queue. The Creditmemo will be created automatically as soon as Ogone sends an acknowledgement.')
                );
                $this->getHelper()->redirect(
                    Mage::getUrl('*/sales_order/view', array('order_id' => $order->getId()))
                );
            endif;
        }
        catch (Exception $e)
        {
              Mage::getSingleton('core/session')->addError($e->getMessage());
              return $this->_canRefund;
        }
    }
    
    public function cancel(Varien_Object $payment)
    {
        if (true === Mage::registry('ops_auto_void')):
           Mage::unregister('ops_auto_void');
           return parent::cancel($payment);
        endif;
        throw new Mage_Core_Exception($this->getHelper()->__('Please use void to cancel the operation.'));
    }

    public function void(Varien_Object $payment)
    {
         if (true === Mage::registry('ops_auto_void')):
           Mage::unregister('ops_auto_void');
           return parent::void($payment);
        endif;

        $params = Mage::app()->getRequest()->getParams();
        $order = Mage::getModel("sales/order")->load($params['order_id']);
        $orderID = $payment->getOrder()->getId();
        
        $alreadyCaptured = Mage::helper('ops/order_void')->getCapturedAmount($order);
        $voidAmount = $order->getGrandTotal() - $alreadyCaptured;

        $requestParams  = array(
            'AMOUNT' => $voidAmount * 100,
            'ORDERID' => $order->getQuoteId(),
            'OPERATION' => self::OPS_DELETE_AUTHORIZE
        );

        if (Mage::helper('ops/directlink')->checkExistingTransact(self::OPS_VOID_TRANSACTION_TYPE,  $orderID)){
            $this->getHelper()->redirectNoticed($orderID, $this->getHelper()->__('You already sent a void request. Please wait until the void request will be acknowledged.'));
        }
        if (Mage::helper('ops/directlink')->checkExistingTransact(self::OPS_CAPTURE_TRANSACTION_TYPE,  $orderID)){
            $this->getHelper()->redirectNoticed($orderID, $this->getHelper()->__('There is one capture request waiting. Please wait until this request is acknowledged.'));
        }

        try {
            $url = Mage::getModel('ops/config')->getDirectLinkGatewayPath();
            $response = Mage::getSingleton('ops/api_directlink')->performRequest($requestParams, $url);
            Mage::helper('ops/payment')->saveOpsStatusToPayment($payment, $response);

            if ($response['STATUS'] == self::OPS_VOID_WAITING || $response['STATUS'] == self::OPS_VOID_UNCERTAIN):
                Mage::helper('ops/directlink')->directLinkTransact(
                   Mage::getSingleton("sales/order")->loadByIncrementId($payment->getOrder()->getIncrementId()), // reload order to avoid canceling order before confirmation from ops
                   $response['PAYID'],
                   $response['PAYIDSUB'],
                   array(
                       'amount' => $voidAmount,
                       'void_request' => Mage::app()->getRequest()->getParams(),
                       'response'     => $response,
                   ),
                   self::OPS_VOID_TRANSACTION_TYPE,
                   Mage::helper('ops')->__('Start Ogone void request. Ogone status: %s.', $this->getHelper()->getStatusText($response['STATUS'])));
                $this->getHelper()->redirectNoticed($orderID, $this->getHelper()->__('The void request is sent. Please wait until the void request will be accepted.'));
            elseif ($response['STATUS'] == self::OPS_VOIDED || $response['STATUS'] == self::OPS_VOIDED_ACCEPTED):
                Mage::helper('ops/directlink')->directLinkTransact(
                   Mage::getSingleton("sales/order")->loadByIncrementId($payment->getOrder()->getIncrementId()), // reload order to avoid canceling order before confirmation from ops
                   $response['PAYID'],
                   $response['PAYIDSUB'],
                   array(),
                   self::OPS_VOID_TRANSACTION_TYPE,
                   $this->getHelper()->__('Void order succeed. Ogone status: %s.',$response['STATUS']),
                   1);
                return parent::void($payment);
            else: 
                Mage::throwException($this->getHelper()->__('Void order failed. Ogone status: %s.',$response['STATUS']));
            endif;
        }
        catch (Exception $e){
            Mage::helper('ops')->log("Exception in void request:".$e->getMessage());
            throw new Mage_Core_Exception($e->getMessage());
        }
    }

    /** ops payment code */
    protected function getOpsCode() {
        return ucfirst(substr($this->_code, strpos($this->_code, '_')+1));
    }

    public function getOpsBrand() {
        return $this->getOpsCode();
    }
}
