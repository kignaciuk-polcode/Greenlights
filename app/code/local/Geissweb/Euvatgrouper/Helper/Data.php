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
class Geissweb_Euvatgrouper_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Gets the default country from Store Config
     * @return bool
     */
    public function getStoreCountryCode()
    {
	return Mage::getStoreConfig('general/country/default', Mage::app()->getStore()->getId());
    }

    /**
     * Determines if the validation of VAT-IDs is enabled
     * @return int
     */
    public function isValidationEnabled()
    {
	return Mage::getStoreConfig('euvatgrouper/vat_settings/validate_vatid', Mage::app()->getStore()->getId());
    }

    /**
     * Gets a list of all current EU member states
     * @return array
     */
    public function getEuCountries()
    {
	$list = str_replace(" ", "", Mage::getStoreConfig('euvatgrouper/vat_settings/member_states'));
	return explode(",", $list);
    }

    /**
     * Determines wether the country is a member state or not
     * @param $cc : 2-letter country code
     * @return bool
     */
    public function isEuCountry($cc)
    {
	if (in_array($cc, $this->getEuCountries()))
	    return true;
	return false;
    }

    /**
     * Gets the full store VAT-ID
     * @return int
     */
    public function getShopVatId()
    {
	return Mage::getStoreConfig('euvatgrouper/vat_settings/own_vatid', Mage::app()->getStore()->getId());
    }

    /**
     * Gets the CC from VAT-ID
     * @return int
     */
    public function getShopVatCc()
    {
	return substr(Mage::getStoreConfig('euvatgrouper/vat_settings/own_vatid', Mage::app()->getStore()->getId()), 0, 2);
    }

    /**
     * Gets the mail sender address
     * @return int
     */
    public function getMailSender()
    {
	return Mage::getStoreConfig('euvatgrouper/vat_settings/mail_sender', Mage::app()->getStore()->getId());
    }

    /**
     * Gets the mail recipient address
     * @return int
     */
    public function getMailRecipient()
    {
	return Mage::getStoreConfig('euvatgrouper/vat_settings/mail_recipient', Mage::app()->getStore()->getId());
    }

    /**
     * Gets the mail template
     * @return int
     */
    public function getMailTemplate()
    {
	return Mage::getStoreConfig('euvatgrouper/vat_settings/mail_template', Mage::app()->getStore()->getId());
    }

    /*
      public function getShowAddressFields() {
      return Mage::getStoreConfig('euvatgrouper/reg_settings/show_address');
      }
      public function getStoreCountryName()
      {
      return Mage::getModel('directory/country')->load($this->getStoreCountry())->getName();
      }
     */
}

?>