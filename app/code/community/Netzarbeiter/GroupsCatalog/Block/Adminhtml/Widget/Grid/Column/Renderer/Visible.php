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

class Netzarbeiter_GroupsCatalog_Block_Adminhtml_Widget_Grid_Column_Renderer_Visible
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
	public function renderHeader()
	{
		if($this->getColumn()->getHeader()) {
			return parent::renderHeader();
		}

		$checked = '';
		if ($filter = $this->getColumn()->getFilter()) {
			$checked = $filter->getValue() ? 'checked="checked"' : '';
		}
		return '<input type="checkbox" name="'.$this->getColumn()->getFieldName().'" onclick="'.$this->getColumn()->getGrid()->getJsObjectName().'.checkCheckboxes(this); visibleProducts.updateProductStates(this);" class="checkbox" '.$checked.' title="'.Mage::helper('adminhtml')->__('Select All').'"/>';
	}
}