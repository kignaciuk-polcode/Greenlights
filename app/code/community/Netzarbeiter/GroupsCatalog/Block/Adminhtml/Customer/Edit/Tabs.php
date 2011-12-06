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
 * Add the "Visible Products" tag to the customer edit page navigation.
 * I wish it would be possible to specify the order of the tabs by adding a
 * position index.
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_GroupsCatalog_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
	protected function _beforeToHtml()
	{
		if (Mage::registry('current_customer')->getId())
		{
			$this->addTab('visible_products', array(
					'label'     => $this->__('Visible Products'),
					'class'     => 'ajax',
					'url'       => $this->getUrl('*/*/visibleProducts', array('_current' => true)),
					'after'		=> 'tags',
				));
		}
		return parent::_beforeToHtml();
	}
}