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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer new password field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Renderer_Product extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element) 
	{			  
		$html = '<tr>
					<td class="label"><label for="product">Product <span class="required">*</span></label></td>
					<td class="value"><span id="product_details_select"><a href="" onClick="document.getElementById(\'multipledeals_tabs_form_section\').removeClassName(\'active\'); document.getElementById(\'multipledeals_tabs_products_section\').addClassName(\'active\');document.getElementById(\'multipledeals_tabs_products_section_content\').style.display = \'\'; document.getElementById(\'multipledeals_tabs_form_section_content\').style.display = \'none\'; return false;">Select a Product</a></span><div style="display:none;" id="advice-required-entry-product_details_select" class="validation-advice">This is a required field.</div></td>	
				</tr>
				<tr style="display:none;">
					<td class="label"><label for="product">Product Name</label></td>
					<td class="value"><span id="product_details_name">Please Select a Product First.</span><p class="note" id="name_note" style="color:#666; display:none;"><span><a href="" onClick="document.getElementById(\'multipledeals_tabs_form_section\').removeClassName(\'active\'); document.getElementById(\'multipledeals_tabs_products_section\').addClassName(\'active\');document.getElementById(\'multipledeals_tabs_products_section_content\').style.display = \'\'; document.getElementById(\'multipledeals_tabs_form_section_content\').style.display = \'none\'; return false;">Change Product</a> || <a target="blank" id="name_product_edit" href="#">Edit Product</a></span></p></td>	
					<input type="hidden" style="position:absolute;" class="input-text" value="'.$element->getValue().'" name="product_id" id="product_id" />
				</tr>				
				<tr style="display:none;">		
					<td class="label"><label for="product">Product Price</label></td>
					<td class="value"><span id="product_details_price">Please Select a Product First.</span><p class="note" id="price_note" style="color:#666; display:none;"><span><a href="" onClick="document.getElementById(\'multipledeals_tabs_form_section\').removeClassName(\'active\'); document.getElementById(\'multipledeals_tabs_products_section\').addClassName(\'active\');document.getElementById(\'multipledeals_tabs_products_section_content\').style.display = \'\'; document.getElementById(\'multipledeals_tabs_form_section_content\').style.display = \'none\'; return false;">Change Product</a> || <a target="blank" id="price_product_edit" href="#">Edit Product</a></span></p></td>					
				</tr>    		
				<tr style="display:none;">		
					<td class="label"><label for="product">Product Qty</label></td>
					<td class="value"><span id="product_details_qty">Please Select a Product First.</span><p class="note" id="qty_note" style="color:#666; display:none;"><span><a href="" onClick="document.getElementById(\'multipledeals_tabs_form_section\').removeClassName(\'active\'); document.getElementById(\'multipledeals_tabs_products_section\').addClassName(\'active\');document.getElementById(\'multipledeals_tabs_products_section_content\').style.display = \'\'; document.getElementById(\'multipledeals_tabs_form_section_content\').style.display = \'none\'; return false;">Change Product</a> || <a target="blank" id="qty_product_edit" href="#">Edit Product</a></span></p></td>					
				</tr>';
		
		
        return $html;
    }		

}
