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
 * Adjust the cache for the catalog navigation to be cached
 * depending on the customer group
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_GroupsCatalog_Block_Navigation extends Mage_Catalog_Block_Navigation
{
	/**
	 * Set the module translation namespace
	 */
	public function _construct()
	{
		$this->setData('module_name', 'Mage_Catalog');
		parent::_construct();
	}
	
	/**
	 * Set this so the navigation is cached depending on the customer group.
	 * Otherwise, the cached navigation could shown a state not matching the current customer group
	 * 
	 * @return string
	 */
    public function getCacheKey()
    {
		$key = parent::getCacheKey();
		$key .= Mage::helper('groupscatalog')->getCustomerGroupId();
        return $key;
    }
}
