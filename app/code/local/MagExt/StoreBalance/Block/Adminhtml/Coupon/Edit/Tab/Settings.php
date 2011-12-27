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
 * Coupon settings tab
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Coupon_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_sectionCode      = 'magext_storebalance';
    protected $_sectionGroupCode = 'storebalance_coupons';
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Adminhtml/Block/Widget/Mage_Adminhtml_Block_Widget_Form#_prepareForm()
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_storebalance_coupon');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('coupon_settings_');
        $form->setFieldNameSuffix('settings');
        
        $fieldset = $form->addFieldset('settings_fieldset', array('legend'=>$this->_helper()->__('Settings')));
        foreach ($this->_getSettingsFields() as $code => $field)
        {
            if (!empty($field['frontend_model']))
            {
                $fieldRenderer = Mage::getBlockSingleton((string)$field['frontend_model']);
            }
            else 
            {
                $fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
            }
            $fieldRenderer->setForm($this);
            
            $fieldConfig = array(
                'name'      => $code,
                'label'     => $field['label'],
                'title'     => $field['label'],
                'comment'   => !empty($field['comment']) ? (string)$field['comment'] : '',
                'class'     => !empty($field['validate']) ? $field['validate'] : '',
            );
            if ($field['frontend_type'] == 'select' && !empty($field['source_model']))
            {
                $fieldConfig['values'] = Mage::getSingleton($field['source_model'])->toOptionArray();
            }
            $checked  = ' checked="checked" ';
            $checkboxLabel = 'Use Config Settings';
            $defText = Mage::getStoreConfig($this->_sectionCode.'/'.$this->_sectionGroupCode.'/'.$code);
            $html = '<input id="'.$code.'_use_config" name="use_config['.$code.']" type="checkbox" value="1" class="checkbox" '.$checked.' onclick="toggleValueElements(this, this.parentNode)" /> ';
            $html.= '<label for="'.$code.'_use_config" title="'.htmlspecialchars($defText).'">'.$checkboxLabel.'</label>';
            $fieldConfig['after_element_html'] = $html;
            
            $formField  = $fieldset->addField($code, $field['frontend_type'], $fieldConfig);
            $formField->setDisabled(true);
            $formField->setRenderer($fieldRenderer);
        }
        $form->setValues(Mage::getStoreConfig($this->_sectionCode.'/'.$this->_sectionGroupCode));
        $form->addValues($model->getData());
        
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    /**
     * Get default settings values from config
     */
    protected function _getSettingsFields()
    {
        $data = Mage::getStoreConfig($this->_sectionCode.'/'.$this->_sectionGroupCode);
        $configFields = Mage::getSingleton('adminhtml/config');
        /* @var $configFields Mage_Adminhtml_Model_Config */
        
        $section = $configFields->getSection($this->_sectionCode);
        $fields = array();
        if (isset($section->groups->{$this->_sectionGroupCode}->fields) && $section->groups->{$this->_sectionGroupCode}->fields->hasChildren())
        {
            $fields = $section->groups->{$this->_sectionGroupCode}->fields->asArray();
            foreach ($fields as $k=>$field)
            {
                $fields[$k]['value'] = $data[$k];
            }
        }
        return $fields;
    }
    
    /**
     * Retrieve helper
     * @return MagExt_StoreBalance_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mgxstorebalance');
    }
}