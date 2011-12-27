<?php

class Polcode_Offer_Block_Adminhtml_Inquiry_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('inquiry_form', array('legend' => Mage::helper('offer/inquiry')->__('Inquiry information')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('offer/inquiry')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('offer/inquiry')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('offer/inquiry')->__('Active'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('offer/inquiry')->__('Inactive'),
                ),
            ),
        ));

//            $fieldset->addField('content', 'editor', array(
//                'name'      => 'content',
//                'label'     => Mage::helper('<module>')->__('Content'),
//                'title'     => Mage::helper('offer/inquiry')->__('Content'),
//                'style'     => 'width:98%; height:400px;',
//                'wysiwyg'   => false,
//                'required'  => true,
//            ));

        if ( Mage::getSingleton('adminhtml/session')->getInquiryData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getInquiryData());
            Mage::getSingleton('adminhtml/session')->setInquiryData(null);
        } elseif ( Mage::registry('inquiry_data') ) {
            $form->setValues(Mage::registry('inquiry_data')->getData());
        }
        return parent::_prepareForm();
    }

}
