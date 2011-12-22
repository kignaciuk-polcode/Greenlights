<?php
/**
 * MagExtension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagExtension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magextension.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magextension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magextension.com for more information.
 *
 * @category   MagExt
 * @package    MagExt_StoreBalance
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */
 
/**
 * Events observer model
 *
 * @author  MagExtension Development team
 */ 
class MagExt_StoreBalance_Model_Observer
{
    /**
     * Set Coupon model to Coupon History model
     * @param Varien_Event_Observer $observer
     */
    public function saveCouponAfter(Varien_Event_Observer $observer)
    {
        $coupon = $observer->getEvent()->getCoupon();
        $coupon->getHistoryModel()
            ->setCouponModel($coupon)
            ->save();
    }
    
    /**
     * Set Balance model to Balance Transaction model
     * @param Varien_Event_Observer $observer
     */
    public function saveBalanceAfter(Varien_Event_Observer $observer)
    {
        $balance = $observer->getEvent()->getBalance();
        $balance->getTransactModel()
            ->setBalanceModel($balance)
            ->save();
    }
    
    /**
     * Add Store Balance data from POST to customer's model data
     * @param Varien_Event_Observer $observer
     */
    public function prepareCustomerSave(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $request  = $observer->getEvent()->getRequest();
        if ($data = $request->getPost('storebalance'))
        {
            $customer->setStoreBalanceData($data);
        }
    }
    
    /**
     * Save Store Balance information for customer
     * @param Varien_Event_Observer $observer
     */
    public function saveCustomerAfter(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $storebalance = Mage::getModel('mgxstorebalance/balance');
        if (($data = $customer->getStoreBalanceData()) && !empty($data['value_change']))
        {
            $storebalance->setData($data)
                ->setCustomer($customer)
                ->save();
        }
    }
    
    /**
     * Set totals collected flag for quote to false
     * @param Varien_Event_Observer $observer
     */
    public function collectQuoteTotalsBefore(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setStoreBalanceTotalsCollected(false);
    }
    
    /**
     * Checks if Store Balance is enough to complete order
     * @param Varien_Event_Observer $observer
     */
    public function placeOrderBefore(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        /* @var $order Mage_Sales_Model_Order */
        
        if (!Mage::helper('mgxstorebalance')->isEnabled() || $order->getPayment()->getMethodInstance()->getCode() != 'storebalance') {
            return;
        }
        
        $error = '';
        if ($order->getBaseStoreBalanceAmount() > 0) 
        {
            $balance = Mage::getModel('mgxstorebalance/balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
                ->loadBalance()
                ->getValue();

            if (($order->getBaseStoreBalanceAmount() - $balance) >= 0.0001)
            {
            	$error = Mage::helper('mgxstorebalance')->__('Your Store Balance is not enough to complete this Order.');
            }
        }
        else
        {
            $error = Mage::helper('mgxstorebalance')->__('An error occured while placing an order using Store Balance. Try to use another payment method.');
        }
        if ($error)
        {
            Mage::getSingleton('checkout/type_onepage')
                ->getCheckout()
                ->setUpdateSection('payment-method')
                ->setGotoSection('payment');
            Mage::throwException($error);
        }
    }
    
    /**
     * Decrease Store balance by Orders grand total value after placing order
     * @param Varien_Event_Observer $observer
     */
    public function decreaseStoreBalance(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('mgxstorebalance')->isEnabled()) {
            return;
        }
        $order = $observer->getEvent()->getOrder();
        /* @var $order Mage_Sales_Model_Order */
        if ($order->getBaseStoreBalanceAmount() > 0) 
        {
            //decrease balance
            Mage::getModel('mgxstorebalance/balance')->useBalance($order);
        }
    }
    
    /**
     * Increase order invoiced amount by invoice store balance amount
     * @param Varien_Event_Observer $observer
     */
    public function saveInvoiceAfter(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $order = $invoice->getOrder();
        
        if ($invoice->getBaseStoreBalanceAmount()) {
            $order->setBaseStoreBalanceInvoiced($order->getBaseStoreBalanceInvoiced() + $invoice->getBaseStoreBalanceAmount());
            $order->setStoreBalanceInvoiced($order->getStoreBalanceInvoiced() + $invoice->getStoreBalanceAmount());
        }
        
        $qty = 0;
        foreach ($invoice->getAllItems() as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getStoreBalanceRefill()) {
                $qty += $item->getQty();
            }
        }
        if ($qty) {
            Mage::getModel('mgxstorebalance/balance')->refillByInvoice($invoice, $qty);
        }
    }
    
    /**
     * Checks the possibility to make a creditmemo for order
     * @param Varien_Event_Observer $observer
     */
    public function loadOrderAfter(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->getState() === Mage_Sales_Model_Order::STATE_CANCELED ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
            return $this;
        }
        

        if (abs($order->getStoreBalanceInvoiced() - $order->getStoreBalanceRefunded())<.0001) {
            return $this;
        }
        $order->setForcedCanCreditmemo(true);
        
        return $this;
    }
    
    /**
     * Increase order refunded amout by creditmemo store balance amount.
     * Checks the possibility to make a creditmemo for order
     * @param Varien_Event_Observer $observer
     */
    public function refundCreditmemo(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        if ($creditmemo->getBaseStoreBalanceAmount()) 
        {
            $order->setBaseStoreBalanceRefunded($order->getBaseStoreBalanceRefunded() + $creditmemo->getBaseStoreBalanceAmount());
            $order->setStoreBalanceRefunded($order->getStoreBalanceRefunded() + $creditmemo->getStoreBalanceAmount());
            
            if (abs($order->getStoreBalanceInvoiced() - $order->getStoreBalanceRefunded())<.0001) {
                $order->setForcedCanCreditmemo(false);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     */
    public function saveCreditmemoAfter(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        
        if ($creditmemo->getBaseStoreBalanceAmount()) 
        {
            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
            Mage::getModel('mgxstorebalance/balance')->refund($creditmemo);
        }
        return $this;
    }

    /**
     * Disables Free payment method output in method list 
     * after activating Store Balance payment method
     * 
     * @param Varien_Event_Observer $observer
     */
    public function disableFreePaymentMethod(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Payment_Block_Form_Container) {
            if ($block->getQuote()->getBaseStoreBalanceTotal()) {
                $methods = array();
                foreach ($block->getMethods() as $_method)
                {
                    if ($_method->getCode() != 'free')
                        $methods[] = $_method;
                }
                $block->setData('methods', $methods);
            }
        }
    }
    
    /**
     * Redirects to checkout after click on "Proceed to Checkout" button
     *  
     * @param Varien_Event_Observer $observer
     */
    public function redirectToCheckout(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();
        $response = $observer->getEvent()->getResponse();
        
        if ($request->getParam('gotocheckout'))
        {
            $message = Mage::helper('mgxstorebalance')->__('%s was successfully added to your shopping cart.', $product->getName());
            Mage::getSingleton('checkout/session')->addSuccess($message);
            $response->setRedirect(Mage::getUrl('checkout'))->sendResponse();
            exit;
        }
    }
    
    /**
     * Creates replanishment product after configuration saving
     *  
     * @param Varien_Event_Observer $observer
     */
    public function saveConfigBefore(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        if (!isset($object)) {
            $object = $observer->getEvent()->getDataObject();
        }
        if ($object instanceof Mage_Core_Model_Config_Data && $object->getPath() == 'magext_storebalance/storebalance_purchase/product')
        {
            $groups = $object->getGroups();
            if ($groups && isset($groups['storebalance_purchase']['fields']['product']['manage']))
            {
                $this->_createReplenishmentProduct();
            }
        }
    }
    
    protected function _createReplenishmentProduct()
    {
        $product = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        if (Mage::helper('mgxstorebalance')->isAvailableProductCreate($product))
        {
            $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
            
            //get default product attribute set
            $defaultSetId = Mage::getModel('eav/entity_type')
                ->loadByCode('catalog_product')
                ->getDefaultAttributeSetId();
            $product->setAttributeSetId($defaultSetId); // need to look this up
            $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL);
            $product->setStoreId($storeId);
            
            $product->setSku("store_balance_refill");
            $product->setName("Store Balance Units");
            $product->setDescription("This product allows you to buy Store Balance Units");
            $product->setShortDescription("This product allows you to buy Store Balance Units");
            $product->setPrice(1);
            
            $product->setTaxClassId(0); // none
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG); // catalog
            $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
             
            // assign product to all websites
            $allWebsites = $product->getResource()->getReadConnection()
            ->fetchPairs($product->getResource()->getReadConnection()
                ->select()
                ->from($product->getResource()->getTable('core_website'), array('website_id', 'website_id'))
                ->where($product->getResource()->getReadConnection()->quoteInto('website_id>?', 0))
            );
            if ($allWebsites)
                $product->setWebsiteIds($allWebsites);
            
            //use product for buying Store Balance Units
            $product->setStoreBalanceRefill(1);
            
            $stockData['manage_stock'] = 0;
            $stockData['use_config_manage_stock'] = 0;
            $stockData['min_sale_qty'] = 10;
            $stockData['use_config_min_sale_qty'] = 0;
            $stockData['use_config_max_sale_qty'] = 1;
            $product->setStockData($stockData);
            
            $product->save();
        }
    }
}