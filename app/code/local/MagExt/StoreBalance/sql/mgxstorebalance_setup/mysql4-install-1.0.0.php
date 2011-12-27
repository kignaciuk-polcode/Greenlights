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
 
$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('mgxstorebalance')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mgxstorebalance')} (
  `balance_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL,
  `value` decimal(12,4) NOT NULL,
  PRIMARY KEY (`balance_id`),
  KEY `FK_STOREBALANCE_CUSTOMER_ENTITY` (`customer_id`),
  CONSTRAINT `FK_STOREBALANCE_CUSTOMER_ENTITY` FOREIGN KEY (customer_id) REFERENCES `{$this->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `FK_STOREBALANCE_CORE_WEBSITE` ( `website_id` ),
  CONSTRAINT `FK_STOREBALANCE_CORE_WEBSITE` FOREIGN KEY ( website_id ) REFERENCES {$this->getTable('core_website')} ( `website_id` ) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('mgxstorebalance_transact')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mgxstorebalance_transact')} (
  `transact_id` int(10) unsigned NOT NULL auto_increment,
  `balance_id` int(10) unsigned NOT NULL,
  `action` TINYINT( 1 ) UNSIGNED NOT NULL,
  `modified_date` DATETIME default NULL,
  `value` decimal(12,4) NOT NULL,
  `value_change` decimal(12,4) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`transact_id`),
  KEY `FK_STOREBALANCE_TRANSACT_STOREBALANCE` (`balance_id`),
  CONSTRAINT `FK_STOREBALANCE_TRANSACT_STOREBALANCE` FOREIGN KEY (balance_id) REFERENCES `{$this->getTable('mgxstorebalance')}` (`balance_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('mgxstorebalance_coupon')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mgxstorebalance_coupon')} (
  `coupon_id` int(10) unsigned NOT NULL auto_increment,
  `website_id` smallint(5) unsigned NOT NULL,
  `hash` varchar(255) NOT NULL,
  `balance` decimal(12,4) NOT NULL,
  `created_date` DATETIME default NULL,
  `updated_date` DATETIME default NULL,
  `used_date` DATE default NULL,
  `from_date` DATE default NULL,
  `to_date` DATE default NULL,
  `is_active` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`coupon_id`),
  KEY `FK_STOREBALANCE_COUPON_CORE_WEBSITE` ( `website_id` ),
  CONSTRAINT `FK_STOREBALANCE_COUPON_CORE_WEBSITE` FOREIGN KEY ( website_id ) REFERENCES {$this->getTable('core_website')} ( `website_id` ) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('mgxstorebalance_coupon_history')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mgxstorebalance_coupon_history')} (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_id` int(10) unsigned NOT NULL,
  `action` TINYINT(1) UNSIGNED NOT NULL,
  `modified_date` DATETIME default NULL,
  `balance` decimal(12,4) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`history_id`),
  KEY `FK_STOREBALANCE_COUPON_HISTORY_STOREBALANCE_COUPON` (`coupon_id`),
  CONSTRAINT `FK_STOREBALANCE_COUPON_HISTORY_STOREBALANCE_COUPON` FOREIGN KEY (coupon_id) REFERENCES `{$this->getTable('mgxstorebalance_coupon')}` (`coupon_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->addAttribute('quote', 'store_balance_total', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_store_balance_total', array('type'=>'decimal'));

$installer->addAttribute('quote_address', 'store_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'base_store_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'store_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_store_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'store_balance_invoiced', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_store_balance_invoiced', array('type'=>'decimal'));

$installer->addAttribute('order', 'store_balance_refunded', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_store_balance_refunded', array('type'=>'decimal'));

$installer->addAttribute('invoice', 'store_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'base_store_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'store_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'base_store_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('catalog_product', 'store_balance_refill', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Use product to refill Store Balance',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '0',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => '',
    'is_configurable'   => false
));
$installer->endSetup();