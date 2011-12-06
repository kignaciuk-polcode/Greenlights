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


class Netresearch_OPS_Block_Form extends Mage_Payment_Block_Form_Cc
{
    /**
     * Init OPS payment form
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ops/form.phtml');
    }

    /**
     * get OPS config
     *
     * @return Netresearch_Ops_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('ops/config');
    }

    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getCcBrands()
    {
        return explode(',', $this->getConfig()->getAcceptedCcTypes());
    }

    public function getDirectDebitCountryIds()
    {
        return explode(',', $this->getConfig()->getDirectDebitCountryIds());
    }

    public function getBankTransferCountryIds()
    {
        return explode(',', $this->getConfig()->getBankTransferCountryIds());
    }

    public function getPSPID()
    {
        return Mage::getModel('ops/config')->getPSPID();
    }

    public function getAcceptUrl()
    {
        return Mage::getModel('ops/config')->getAcceptUrl();
    }

    public function getExceptionUrl()
    {
        return Mage::getModel('ops/config')->getExceptionUrl();
    }

    public function getAliasGatewayUrl()
    {
        return Mage::getModel('ops/config')->getAliasGatewayUrl();
    }

    public function getSaveCcBrandUrl()
    {
        return Mage::getModel('ops/config')->getSaveCcBrandUrl();
    }

    public function getGenerateHashUrl()
    {
        return Mage::getModel('ops/config')->getGenerateHashUrl();
    }

    public function getCcSaveAliasUrl()
    {
        return Mage::getModel('ops/config')->getCcSaveAliasUrl();
    }
}
