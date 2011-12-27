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
 
/**
 * System Config Replenishment product field renderer
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_System_Config_Form_Field_Product 
    extends Mage_Adminhtml_Block_System_Config_Form_Field 
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        // replace [value] with [manage]
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
        
        $checkboxLabel = Mage::helper('mgxstorebalance')->__('Create Preconfigured Product');
        $oldComment = $element->getComment();
        $newComment = Mage::helper('mgxstorebalance')->__('To be able to select the product please tick the checkbox and save settings.');
        
        $afterHtml = <<<AFTER_HTML
        <p class="nm note"><small>{$oldComment}</small></p>
        <input id="{$id}_manage" name="{$namePrefix}[manage]" type="checkbox" value=1 class="checkbox config-inherit" />
        <label for="{$id}_manage" class="inherit" title="">{$checkboxLabel}</label>
AFTER_HTML;
        if (Mage::helper('mgxstorebalance')->isAvailableProductCreate()) {
            $element->setAfterElementHtml($afterHtml);
            $element->setComment($newComment);
        }
        return parent::render($element);
    }
}