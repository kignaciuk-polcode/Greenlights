
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

Magento Module: Netzarbeiter/GroupsCatalog
Author: Vinai Kopp <vinai@netzarbeiter.com>

This extension enables you to hide categories and products from customers
depending on the customer group.
You can specify a default for all categories and products under
System / Catalog / Customer Groups Catalog

There you can also choose to disable the extension.

The default after installation is no categories or products are hidden.
You can override the default from te configuration in the Product Management
and Category Management pages.

You can also set the products visible to a customers group on the "Visible Products" Tab
when editing customers.

If you have 20 or more groups and complex configurations, you may want to enable the
dynamic field resize feature in the config settings, so the extension can grow the field
size when needed. This feature is turned off by default as a safety precaution, because
it is changing a core magento database table.

This extension is a lot more flexible then the Netzarbeiter_LoginCatalog and
Netzarbeiter_CustomerActivation modules, but it is also more complex.
I suggest not installing this extension together with Netzarbeiter_LoginCatalog,
as that doesn't really make sense.


If you ever uninstall the extension (I don't hope so ;)) your site will be broken, because
Magento doesn't support a mechanism to remove attributes with an extension, and this
extension uses source models.
To fix the Error, you have to execute the following SQL:

   DELETE FROM `eav_attribute` WHERE attribute_code LIKE 'groupscatalog%';
   DELETE FROM `core_resource` WHERE code = 'groupscatalog_setup';

Don't forget to clear the cache, afterwards.

CHANGES:
Release 0.4.7 Fix another bug introduced during the refactoring in versin 0.4.5
Release 0.4.6 Fix bugs introduced during the previous update. Sorry!
Release 0.4.5 Fix GroupsCatalog to work with the Magento 1.5 ImportExport module
Release 0.4.4 Fix typo in an error message from the attributes backend validate() method.
Release 0.4.3 Fix another bug related to the layered navigation.
Release 0.4.2 Fix the isAnchor Bug - thanks to Gabriel Heter for the pointer to the patch!
Release 0.4.1 Fix the result count in the advanced search
Release 0.4.0 Added redirect on product hidden page (thanks to Jonathan Day for the original patch!)
Release 0.3.9 Magento 1.4.2 compatibility release
Release 0.3.8 Bugfix, now also filters out hidden products from bundled product options
Release 0.3.7 Select default value in backend for products and catgories without any value for the hide_group attibute
Release 0.3.6 Some changes to fix hiding categories in the top navigation in 1.4.1.1
Release 0.3.5 Add attribute backend model
Release 0.3.4 Add category attribute to attribute set and group so it can be accessed in the backend
Release 0.3.3 Magento 1.4 compatibility update
Release 0.3.2 Make extension work with SOAP/XML-RPC API
Release 0.3.1 Dutch translation (nl_NL) included, thanks to Dirk Dinnewet!
Release 0.3.0 Make the "select all" checkbox in the visible products grid usable
Release 0.2.9 Update product attribute backend model
Release 0.2.8 Fix the bugfix *sigh* Sorry about that
Release 0.2.7 Fix bug in customergroups source model when saving a product after adding it to a new category (Thanks to Don Gilbert!)
Release 0.2.6 The Edit Customer Page has a new tab to enable/disable product accessibility for the customers group
              Also, if enabled in the config section the db field size can be dynamically resized if required for complex permission configurations with 20+ groups.
Release 0.2.5 Magento 1.3.1 compatibility issues (more to come with one of the next mage releases... had to work around awkward implementation quirks)
Release 0.2.4 fixed translaton namespace in layered navigation 
Release 0.2.3 add compatibility to Magento 0.1.3 and the flat catalog
Release 0.2.2 added French locale, thanks to vacmar!
Release 0.2.1 try to keep compatibility with Magento 1.1.8 for a bit longer (thanks to Mark Walsham for reporting) 
Release 0.2.0 fixes issue fixed in 0.1.9 but with products
Release 0.1.9 fixes bg with category and items using the global scope name instead of the store view name
Release 0.1.8 cleares the layered navigation cache for products if the groupscatalog config was changed
Release 0.1.7 fixes the block caching of the layered navigation
Release 0.1.6 fixes the layered navigation
Release 0.1.5 fixed sitemap generation - now uses settings for not logged in customers.
Release 0.1.4 fixes the advanced search bug, and features a rewrite of the filter logic
(smaller code base and better compatibility with other modules)
Release 0.1.3 fixes compatibility with 1.1.7 and above
Release 0.1.2: Fixed bug introduced with 0.1.1 (bad start here, sorry for that. Thanks to Tom Arnst for notifying me)
Release 0.1.1: Navigation block caching fixed


KNOWN ISSUES:
- Exporting products hidden to more then one customer group via dataflow: Ungültige Options-ID angegeben für groupscatalog_hide_group (0,1), überspringe den Eintrag

TODO:
- Check compatibility with bundled products, i.e. hide a simple product that is used in a bundled product.
- Make product configuration website scope instead of global.
- When all products in a wishlist are hidden, show the "No items in wishlist" template instead of an empty grid.

If you have ideas for improvements or find bugs, please send them to vinai@netzarbeiter.com,
with Netzarbeiter_GroupsCatalog as part of the subject
