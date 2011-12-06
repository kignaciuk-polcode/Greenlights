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
 * Do not edit or add to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @copyright  Copyright (c) 2011 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product helper
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_GroupsCatalog_Helper_Product extends Mage_Catalog_Helper_Product
{
	const HTTP_REDIRECT_CODE = 303; // 303 See Other

    /**
     * Check if a product can be shown
     *
     * @param  Mage_Catalog_Model_Product|int $product
     * @return boolean
     */
    public function canShow($product, $where = 'catalog')
    {
		if (is_int($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }
        
    	if (parent::canShow($product))
		{
			/*
			 * if is hidden from customer group return false
			 */
			$hiddenForGroup = Mage::helper('groupscatalog')->isProductHidden($product);
			if (! $hiddenForGroup)
			{
				return true;
			}

			if (Mage::getStoreConfig('catalog/groupscatalog/do_hidden_redirect'))
			{
				/*
				 * Display message if non-empty
				 */
				$message = trim(str_replace(
						array('{{sku}}', '{{name}}'),
						array($product->getSku(), $product->getName()),
						Mage::getStoreConfig('catalog/groupscatalog/hidden_message')
				));
				if ('' !== $message)
				{
					Mage::getSingleton('customer/session')->addNotice($message);
				}

				/*
				 * Redirect to configured page
				 */
				Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::app()->getRequest()->getRequestUri());
				$url = Mage::getStoreConfig('catalog/groupscatalog/hidden_redirect');
				Mage::app()->getResponse()
						->setRedirect(Mage::getUrl($url), self::HTTP_REDIRECT_CODE)
						->sendHeaders();
			}
		}
        return false;
    }
}
