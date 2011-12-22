<?php

/**
 * ||GEISSWEB| EU-VAT-GROUPER
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GEISSWEB End User License Agreement
 * that is available through the world-wide-web at this URL:
 * http://www.geissweb.de/eula.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@geissweb.de so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.geissweb.de/ for more information
 * or send an email to support@geissweb.de or visit our customer forum at
 * http://forum.geissweb.de to make a feature request.
 *
 * @category   Mage
 * @package    Geissweb_Euvatgrouper
 * @copyright  Copyright (c) 2011 GEISS Weblösungen (http://www.geissweb.de)
 * @license    http://www.geissweb.de/eula.html GEISSWEB End User License Agreement
 */
class Geissweb_Euvatgrouper_Model_Observer extends Mage_Checkout_Model_Observer {

    var $DEBUG = false;

    /*
     * observer for the customer saved event
     * @var Varien_Event_Observer
     */

    public function geissweb_customer_save_before(Varien_Event_Observer $observer)
    {
	$customer = $observer->getCustomer();
	$customer_cc = $this->_getBestCustomerCountryCode($customer);
	$vatdata = Mage::getSingleton('customer/session')->getData('_vatgrouper');

	// Just return when admin tries manually to change the customer to another group, group is in excluded list or unable to get customer_cc
	if (Mage::getSingleton('admin/session')->getUser() || in_array($customer->getGroupId(), $this->_getExcludedGroups()) || $customer_cc === false)
	{
	    return $this;
	}

	try {

	    if (is_array($vatdata))
	    {
		$customer->setLastVatValidationDate($vatdata['last_vat_validation_date']);
		$customer->setVatValidationResult($vatdata['vat_validation_result']);
		$customer->setViesResultData($vatdata['vies_result_data']);
	    }
	    $group = $this->_getBestCustomerGroup($vatdata, $customer_cc);
	    $customer->setGroupId($group);
	    return $this;
	} catch (Exception $e) {
	    Mage::log("[GEISSWEB] Fail observer: geissweb_customer_save_before:" . $e->getMessage());
	}
    }

    private function _getBestCustomerCountryCode($customer)
    {
	$customer_ccs = array();

	$customer_ccs[] = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountryId();
	$customer_ccs[] = Mage::getSingleton('sales/quote')->getShippingAddress()->getCountryId();

	if (Mage::getSingleton('customer/session')->isLoggedIn() && ($customer->getDefaultShippingAddress() instanceof Mage_Customer_Model_Address))
	{
	    $customer_ccs[] = $customer->getDefaultShippingAddress()->getCountryId();
	}

	$customer_ccs[] = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getCountryId();
	$customer_ccs[] = Mage::getSingleton('sales/quote')->getBillingAddress()->getCountryId();

	if (Mage::getSingleton('customer/session')->isLoggedIn() && ($customer->getDefaultBillingAddress() instanceof Mage_Customer_Model_Address))
	{
	    $customer_ccs[] = $customer->getDefaultBillingAddress()->getCountryId();
	}

	if ($this->DEBUG === true) Mage::log("[GEISSWEB] User-CCs: " . var_export($customer_ccs, true));

	foreach ($customer_ccs as $cc)
	{
	    if ($cc != NULL)
		return $cc;
	}

	return false;
    }

    private function _getBestCustomerGroup($vatdata, $customer_cc)
    {
	if ($this->DEBUG === true) Mage::log("[GEISSWEB] GETTING GROUP FOR: $customer_cc");
	if (is_array($vatdata))
	{
	    Mage::log("[GEISSWEB] Vatdata OK!");
	    if ($vatdata['customer_taxvat_is_valid'] === true && $vatdata['customer_is_vat_free'] === true)
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] Valid VAT-excempt -> GRP[" . $this->_getValidEuVatGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getValidEuVatGroupId()) . "]");
		return $this->_getValidEuVatGroupId();
	    } elseif ($vatdata['customer_taxvat_is_valid'] === true && $vatdata['customer_is_vat_free'] === false)
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] Valid but full VAT -> GRP[" . $this->_getSameCountryGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getSameCountryGroupId()) . "]");
		return $this->_getSameCountryGroupId();
	    } else
	    {
		if (!Mage::helper('euvatgrouper')->isEuCountry($customer_cc))
		{
		    if ($this->DEBUG === true) Mage::log("[GEISSWEB] OUTSIDE EU -> GRP[" . $this->_getOutsideEuGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getOutsideEuGroupId()) . "]");
		    return $this->_getOutsideEuGroupId();
		} else
		{
		    if ($this->DEBUG === true) Mage::log("[GEISSWEB] DEFAULT -> GRP[" . $this->_getDefaultGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getDefaultGroupId()) . "]");
		    return $this->_getDefaultGroupId();
		}
	    }//endif
	} else
	{

	    if (!Mage::helper('euvatgrouper')->isEuCountry($customer_cc))
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] OUTSIDE EU2 -> GRP[" . $this->_getOutsideEuGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getOutsideEuGroupId()) . "]");
		return $this->_getOutsideEuGroupId();
	    } else
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] DEFAULT2 -> GRP[" . $this->_getDefaultGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getDefaultGroupId()) . "]");
		return $this->_getDefaultGroupId();
	    }
	}
    }

//----------------------------------------------------------------------------------------------------endfunction


    /*
     * Observer for Taxvat-field onchange for logged in customers
     * @var Varien_Event_Observer
     */

    public function geissweb_logged_in_vat_check_after(Varien_Event_Observer $observer)
    {
	try {
	    $customer = $observer->getEvent()->getCustomer();
	    $validation_result = $observer->getEvent()->getValidationResult();

	    if ($validation_result->countryCode . $validation_result->vatNumber == "")
	    {
		$customer->setTaxvat("");
	    }
	    else
	    {
		$customer->setTaxvat($validation_result->countryCode . $validation_result->vatNumber);
	    }

	    $customer->setLastVatValidationDate($validation_result->last_vat_validation_date);
	    $customer->setData('vat_validation_result', $validation_result->validresult);
	    $customer->setViesResultData($validation_result->viesdata);
	    $customer->save();
	    return $this;
	} catch (Exception $e) {
	    Mage::log("[GEISSWEB] Fail observer: logged_in_vat_check_after:" . $e->getMessage());
	}
    }

//----------------------------------------------------------------------------------------------------endfunction


    /*
     * Observer for 'caching' the country id from checkout to avoid a memory leak when switching stores on certain themes
     * @var Varien_Event_Observer
     */

    public function geissweb_customer_address_save_before(Varien_Event_Observer $observer)
    {
	$country_id = $observer->getQuote()->getShippingAddress()->getCountryId();
	if (Mage::getSingleton('customer/session')->getData('_vatgrouper_cc') != $country_id && $country_id != "")
	{
	    Mage::getSingleton('customer/session')->setData('_vatgrouper_cc', $country_id);
	}
    }

//----------------------------------------------------------------------------------------------------endfunction


    /*
     * Observer for tax calculation
     * @var Varien_Event_Observer
     */

    public function geissweb_tax_rate_data_fetch(Varien_Event_Observer $observer)
    {
	$request = $observer->getEvent()->getRequest();
	$product_class = $request->getProductClassId();

	if (Mage::getSingleton('admin/session')->getUser())
	{
	    return $this;
	}

	try {
	    $shop_cc = Mage::helper('euvatgrouper')->getShopVatCc();
	    $session = Mage::getSingleton('customer/session');
	    $vatdata = $session->getData('_vatgrouper');
	    $customer = $session->getCustomer();
	    $user_cc = $session->getData('_vatgrouper_cc');

	    /*
	     * Check if taxvat is already set
	     */
	    if (!$customer_vat_id = $customer->getTaxvat())
	    {
		if ($vatdata)
		{
		    if ($vatdata['customer_taxvat_from_validation'] != "")
		    {
			$customer_vat_id = $vatdata['customer_taxvat_from_validation'];
		    }
		    else
		    {
			$customer_vat_id = "";
		    }
		}
	    }

	    /*
	     * Get country code
	     */
	    if (!$user_cc || $user_cc == "")
	    {
		$user_cc = $this->_getBestCustomerCountryCode($customer);

		if ($customer_vat_id && $user_cc == "")
		{
		    $user_cc = substr($customer_vat_id, 0, 2);
		}
		else
		{
		    $user_cc = $request->getCountryId();
		}
	    }

	    /*
	     * For logged in users with valid vat
	     */
	    if ($session->isLoggedIn())
	    {
		$vatdata = $session->getData('_vatgrouper');
		$customer_has_valid_vat = $vatdata['vat_validation_result'];
	    }
	    else
	    {
		$customer_has_valid_vat = $vatdata['vat_validation_result'];
	    }

	    //Customer has valid VAT-ID and comes not from the same country as the shop
	    if (($customer_has_valid_vat == 1) && ($shop_cc != $user_cc))
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] valid VAT, CCs are different -> customerHasValidVAT: $customer_has_valid_vat | $shop_cc != $user_cc");

		// Handle only the Magento internal request for the target country
		if ($request->getCountryId() == $user_cc)
		{
		    if ($this->DEBUG === true) Mage::log("[GEISSWEB] -> REQ-CC[" . $request->getCountryId() . "] == USERCC[$user_cc] *** GRP[" . $this->_getValidEuVatGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getValidEuVatGroupId()) . "]");
		    $request->setCustomerClassId($this->_getTaxClassIdForGroup($this->_getValidEuVatGroupId()));
		}

		//If Country mismatch between VAT-ID and shipping country
		if ($user_cc != substr($customer_vat_id, 0, 2))
		{
		    if ($this->DEBUG === true) Mage::log("[GEISSWEB] Country Mismatch! -> USR-CC[" . $user_cc . "] != VATCC[" . substr($customer_vat_id, 0, 2) . "]");
		    $request->setCustomerClassId($this->_getTaxClassIdForGroup($this->_getDefaultGroupId()));
		}

		//Customer has valid VAT-ID and comes from the same country as the shop
	    } elseif (($customer_has_valid_vat == 1) && ($shop_cc == $user_cc))
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] valid VAT, CCs are same! -> customerHasValidVAT: $customer_has_valid_vat | $shop_cc , $user_cc");
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] B2B-OwnCountry -> GRP[" . $this->_getOutsideEuGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getOutsideEuGroupId()) . "]");
		if ($request->getCountryId() == $user_cc)
		{
		    $request->setCustomerClassId($this->_getTaxClassIdForGroup($this->_getSameCountryGroupId()));
		}

		//Customer has NO valid VAT-ID and comes from outside EU
	    }
	    elseif (!Mage::helper('euvatgrouper')->isEuCountry($user_cc))
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] NON EU REQUEST -> $shop_cc , $user_cc GRP[" . $this->_getOutsideEuGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getOutsideEuGroupId()) . "]");
		if ($request->getCountryId() == $user_cc)
		{
		    $request->setCustomerClassId($this->_getTaxClassIdForGroup($this->_getOutsideEuGroupId()));
		}

		//Customer is Enduser
	    }
	    else
	    {
		if ($this->DEBUG === true) Mage::log("[GEISSWEB] Default -> $shop_cc, $user_cc GRP[" . $this->_getDefaultGroupId() . "] TXCLS[" . $this->_getTaxClassIdForGroup($this->_getDefaultGroupId()) . "]");
		$request->setCustomerClassId($this->_getTaxClassIdForGroup($this->_getDefaultGroupId()));
	    }//endif

	    if ($this->DEBUG === true) Mage::log("REQ DEBUG: product[$product_class] reqcountry[" . $request->getCountryId() . "] user_cc[" . $user_cc . "] \n" . var_export($request->debug(), true));
	} catch (Exception $e) {
	    Mage::log("[GEISSWEB] Fail observer: geissweb_tax_rate_data_fetch:" . $e->getMessage());
	}
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Observer for sending validation result to shop owner
     * @var Varien_Event_Observer
     */

    public function send_success_mail(Varien_Event_Observer $observer)
    {
	$customer = $observer->getEvent()->getCustomer();
	$results = (array) $observer->getEvent()->getValidationResult();
	foreach ($results as $id => $res)
	{
	    $customer->setData("vies_" . $id, $res);
	}

	$sender = array('name' => Mage::getStoreConfig('trans_email/ident_' . Mage::getStoreConfig('euvatgrouper/vat_settings/mail_sender') . '/name'),
	    'email' => Mage::getStoreConfig('trans_email/ident_' . Mage::getStoreConfig('euvatgrouper/vat_settings/mail_sender') . '/email'));


	Mage::log($sender);
	$vars = array('customer' => $customer);

	$translate = Mage::getSingleton('core/translate');
	Mage::getModel('core/email_template')->sendTransactional(
		Mage::helper('euvatgrouper')->getMailTemplate(), $sender, Mage::helper('euvatgrouper')->getMailRecipient(), Mage::helper('euvatgrouper')->getMailRecipient(), //Recipient Name
		$vars, Mage::app()->getStore()->getId()
	);
	$translate->setTranslateInline(true);
	return $this;
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Observer for making VAT validation data available in session
     * @var Varien_Event_Observer
     */

    public function geissweb_customer_login(Varien_Event_Observer $observer)
    {
	$session = Mage::getSingleton('customer/session');
	$customer = $session->getCustomer();
	try {
	    if ($customer->getVatValidationResult())
	    {
		Mage::log("[GEISSWEB] Res:" . $session->getCustomer()->getVatValidationResult());
		Mage::getSingleton('customer/session')->setData('_vatgrouper', array(
		    'last_vat_validation_date' => $customer->getData('last_vat_validation_date'),
		    'vies_result_data' => $customer->getData('vies_result_data'),
		    'vat_validation_result' => $customer->getData('vat_validation_result'),
		    'customer_taxvat_is_valid' => ($customer->getData('vat_validation_result') == 1) ? true : false,
		    'customer_is_vat_free' => ($customer->getData('vat_validation_result') == 1) ? true : false,
		    'customer_taxvat_from_validation' => $customer->getData('taxvat')
		));
	    }
	} catch (Exception $e) {
	    Mage::log("[GEISSWEB] Fail observer: geissweb_customer_login:" . $e->getMessage());
	}
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Unset the vatgrouper data after guest checkout
     * @var Varien_Event_Observer
     */
    public function geissweb_sales_order_save_after(Varien_Event_Observer $observer)
    {
	if (Mage::getSingleton('admin/session')->getUser())
	{
	    return $this;
	}

	$session = Mage::getSingleton('customer/session');
	if ($observer->getEvent()->getOrder()->getQuote() instanceof Mage_Sales_Model_Quote)
	{
	    if ($observer->getEvent()->getOrder()->getQuote()->getCheckoutMethod() == 'guest' && $session->hasData('_vatgrouper'))
	    {
		unset($session['_vatgrouper']);
	    }
	}
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Observer to bring the own shop vat-id to the order
     * @var Varien_Event_Observer
     */
    public function geissweb_sales_convert_quote_to_order(Varien_Event_Observer $observer)
    {
	if (Mage::getSingleton('admin/session')->getUser())
	{
	    return $this;
	}

	$observer->getEvent()->getOrder()->setShopTaxvat(Mage::getStoreConfig('euvatgrouper/vat_settings/own_vatid'));
	$observer->getEvent()->getOrder()->setCustomerTaxvat($observer->getEvent()->getQuote()->getCustomerTaxvat());

	$session = Mage::getSingleton('customer/session');
	if (!$session->isLoggedIn())
	{
	    if (!$session->hasData('_vatgrouper'))
	    {
		$vatdata = NULL;
	    }
	    else
	    {
		$vatdata = $session->getData('_vatgrouper');
	    }
	    $customer_cc = $this->_getBestCustomerCountryCode($observer->getEvent()->getCustomer());
	    $group_id = $this->_getBestCustomerGroup($vatdata, $customer_cc);
	    $observer->getEvent()->getOrder()->setCustomerGroupId($group_id);
	    $observer->getEvent()->getOrder()->setCustomerTaxClassId($this->_getTaxClassIdForGroup($group_id));
	    if ($this->DEBUG === true) Mage::log("[GEISSWEB] ORDER IS NOW: " . var_export($observer->getEvent()->getOrder()->debug(), true));
	}
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Observer to bring the own shop vat-id and customers vat-id to the invoice
     * @var $observer Varien_Event_Observer
     */
    public function geissweb_sales_convert_order_to_invoice(Varien_Event_Observer $observer)
    {
	$observer->getEvent()->getTarget()->setShopTaxvat(Mage::getStoreConfig('euvatgrouper/vat_settings/own_vatid'));
	$observer->getEvent()->getTarget()->setCustomerTaxvat($observer->getEvent()->getSource()->getCustomerTaxvat());
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Observer to check for the latest version of EU VAT GROUPER
     * @var Varien_Event_Observer
     */
    public function geissweb_check_for_updates()
    {
	$feed = Mage::getSingleton("geissweb_euvatgrouper/feed");
	$feed->checkUpdate();
    }

//----------------------------------------------------------------------------------------------------endfunction

    /*
     * Helper functions
     */
    private function _getExcludedGroups()
    {
	return explode(",", Mage::getStoreConfig('euvatgrouper/grouping_settings/excluded_groups', Mage::app()->getStore()->getId()));
    }

    private function _getValidEuVatGroupId()
    {
	return Mage::getStoreConfig('euvatgrouper/grouping_settings/target_group', Mage::app()->getStore()->getId());
    }

    private function _getSameCountryGroupId()
    {
	return Mage::getStoreConfig('euvatgrouper/grouping_settings/target_group_same_cc', Mage::app()->getStore()->getId());
    }

    private function _getOutsideEuGroupId()
    {
	return Mage::getStoreConfig('euvatgrouper/grouping_settings/target_group_outside', Mage::app()->getStore()->getId());
    }

    private function _getDefaultGroupId()
    {
	return Mage::getStoreConfig('customer/create_account/default_group', Mage::app()->getStore()->getId());
    }

    private function _getTaxClassIdForGroup($group_id)
    {
	return Mage::getSingleton('customer/group')->load($group_id)->getTaxClassId();
    }

}

//----------------------------------------------------------------------------------------------------endclass
?>