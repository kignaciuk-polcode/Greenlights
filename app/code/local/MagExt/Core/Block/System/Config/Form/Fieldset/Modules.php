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
 * @package    MagExt_Core
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */

class MagExt_Core_Block_System_Config_Form_Fieldset_Modules
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $modules = Mage::helper('mgxcore')->getModuleList();
        sort($modules);

        $html.= $this->_getHeadFieldHtml($element);
        foreach ($modules as $module) {
            $html.= $this->_getFieldHtml($element, $module);
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
    
    protected function _getHeadFieldHtml($fieldset)
    {
        $html  = '<tr>';
        $html .= '<td class="label"><label><strong>'.$this->__('Module Name').'</strong></label></td>';
        $html .= '<td class="value"><strong>'.$this->__('Version').'</strong></td>';
        $html .= '</tr>';
        return $html;
    }
    
    protected function _getFieldHtml($fieldset, $module)
    {
        $field = $fieldset->addField((string)$module->id, 'label',
            array(
                'name'          => $module->id,
                'label'         => $module->name ? $module->name : $module->id,
                'value'         => $module->version,
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}