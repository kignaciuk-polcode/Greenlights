<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Who_bought_this_also_bought
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
$installer = $this;
$installer->startSetup();

$installer->run("

DELETE FROM {$this->getTable('relatedproducts/relatedproducts')};
ALTER TABLE {$this->getTable('relatedproducts/relatedproducts')} ADD COLUMN `store_id` SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL AFTER `product_id`;
ALTER TABLE {$this->getTable('relatedproducts/relatedproducts')} ADD KEY `FK_WBTAB_INT_STORE_ID` (`store_id`);
ALTER TABLE {$this->getTable('relatedproducts/relatedproducts')} ADD CONSTRAINT `FK_WBTAB_INT_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

");


$installer->endSetup();
