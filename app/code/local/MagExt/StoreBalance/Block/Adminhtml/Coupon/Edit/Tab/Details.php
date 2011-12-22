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
 * Coupon details tab
 *
 * @author  MagExtension Development team
 */ 
class MagExt_StoreBalance_Block_Adminhtml_Coupon_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$model = Mage::registry('current_storebalance_coupon');
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('coupon_details_');
		$form->setFieldNameSuffix('details');
		
		$fieldset = $form->addFieldset('base_fieldset', array('legend'=>$this->_helper()->__('Details')));
        
		if ($model->getId()) {
            $fieldset->addField('coupon_id', 'hidden', array(
                'name' => 'coupon_id',
            ));
            $fieldset->addField('hash', 'label', array(
                'name'      => 'hash',
                'label'     => $this->_helper()->__('Coupon'),
                'title'     => $this->_helper()->__('Coupon')
            ));
        }
        
        $fieldset->addField('balance', 'text', array(
            'label'     => $this->_helper()->__('Coupon Amount'),
            'title'     => $this->_helper()->__('Coupon Amount'),
            'name'      => 'balance',
            'class'     => 'validate-number',
            'required'  => true,
            'after_element_html'      => '<div id="storebalance_currency_code"></div>',
        ));
        $fieldset->addField('website_id', 'select', array(
            'name'      => 'website_id',
            'label'     => $this->_helper()->__('Website'),
            'title'     => $this->_helper()->__('Website'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true),
        ));
        $fieldset->addField('is_active', 'select', array(
            'label'     => $this->_helper()->__('Status'),
            'title'     => $this->_helper()->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => Mage::getSingleton('mgxstorebalance/coupon')->getStatusOptionArray(),
            'value'     => MagExt_StoreBalance_Model_Coupon::STATUS_ACTIVE,
        ));
        
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => $this->_helper()->__('From Date'),
            'title'  => $this->_helper()->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => $dateFormatIso,
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => $this->_helper()->__('To Date'),
            'title'  => $this->_helper()->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => $dateFormatIso,
        ));
        
        if (!$model->getId()) {
            $fieldset->addField('qty', 'text', array(
                'name'      => 'qty',
                'label'     => $this->_helper()->__('Number of Coupons'),
                'title'     => $this->_helper()->__('Number of Coupons'),
                'class'     => 'validate-number validate-greater-than-zero',
                'required'  => true,
                'value'   => 1,
            ));
        }
        
        if (!$model->getIsNew())
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
	    return $html . $this->getChild('js_currency')->toHtml();
	}
	
	/**
	 * 
	 * @return string
	 */
    public function getWebsiteHtmlId()
    {
        return 'coupon_details_website_id';
    }
	
	/**
	 * 
	 * @return MagExt_StoreBalance_Helper_Data
	 */
	protected function _helper()
	{
	    return Mage::helper('mgxstorebalance');
	}
}