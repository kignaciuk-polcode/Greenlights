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

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

if (version_compare(Mage::getVersion(), '1.4.0', '>='))
{
	try
	{
		$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_flat');
		$process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
		$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_category_flat');
		$process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
	}
	catch (Exception $e)
	{ }
}

$installer->endSetup();