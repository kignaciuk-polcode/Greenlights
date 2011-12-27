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
 * Change Store balance Tab
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Adminhtml_Customer_Edit_Tab_Storebalance_Change extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customer');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('storebalance_');
        $form->setFieldNameSuffix('storebalance');
        
        $fieldset = $form->addFieldset('change_fieldset', array('legend'=>Mage::helper('mgxstorebalance')->__('Change Balance')));
        
        $fieldset->addField('value_change', 'text', array(
            'name'     => 'value_change',
            'label'    => Mage::helper('mgxstorebalance')->__('Amount'),
            'title'    => Mage::helper('mgxstorebalance')->__('Amount'),
            'note'     => Mage::helper('mgxstorebalance')->__('Negative value decreases customer\'s Store Balance'),
            'class'    => 'validate-currency-dollar',
            'after_element_html' => '<div id="storebalance_currency_code"></div>',
        ));
        $fieldset->addField('website_id', 'select', array(
            'name'     => 'website_id',
            'label'    => Mage::helper('mgxstorebalance')->__('Website'),
            'title'    => Mage::helper('mgxstorebalance')->__('Website'),
            'values'   => Mage::getModel('adminhtml/system_store')->getWebsiteValuesForForm(),
        ));
        $fieldset->addField('comment', 'textarea', array(
            'name'     => 'comment',
            'label'    => Mage::helper('mgxstorebalance')->__('Comment'),
            'title'    => Mage::helper('mgxstorebalance')->__('Comment'),
        ));
        
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $jsCusrrency = $this->getLayout()->createBlock('core/template')->setTemplate('mgxstorebalance/currency_js.phtml');
        $this->setChild('js_currency', $jsCusrrency);
    }
    
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html .= $this->getChild('js_currency')->toHtml();;
        return $html;
    }
    
    public function getWebsiteHtmlId()
    {
        return 'storebalance_website_id';
    }
}