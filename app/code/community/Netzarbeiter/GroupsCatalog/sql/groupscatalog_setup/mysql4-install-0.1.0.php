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
 * @var $this Mage_Eav_Model_Entity_Setup
 */
$this->startSetup();

$this->addAttribute('catalog_product', 'groupscatalog_hide_group', array(
	'group'           => 'General',
	'type'            => 'varchar',
	'label'           => 'Hide from customer groups',
	'input'           => 'multiselect',
	'source'          => 'groupscatalog/config_source_customergroups_product',
	'backend'         => 'groupscatalog/entity_attribute_backend_customergroups',
	'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'required'        => 1,
	'default'         => Netzarbeiter_GroupsCatalog_Helper_Data::USE_DEFAULT,
	'user_defined'    => 0,
	//'apply_to'        => 'simple',
	//'is_configurable' => true
));

$this->addAttribute('catalog_category', 'groupscatalog_hide_group', array(
	'type'            => 'varchar',
	'label'           => 'Hide from customer groups',
	'input'           => 'multiselect',
	'source'          => 'groupscatalog/config_source_customergroups_category',
	'backend'         => 'groupscatalog/entity_attribute_backend_customergroups',
	'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'required'        => 1,
	'default'         => Netzarbeiter_GroupsCatalog_Helper_Data::USE_DEFAULT,
	'user_defined'    => 0,
	//'apply_to'        => 'simple',
	//'is_configurable' => true
));

$this->endSetup();


// EOF