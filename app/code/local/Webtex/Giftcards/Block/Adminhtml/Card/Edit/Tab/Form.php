<?php
class Webtex_Giftcards_Block_Adminhtml_Card_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $card = Mage::registry('giftcards_data');
        $helper = Mage::helper('giftcards');
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('giftcards_form', array(
            'legend'=>$helper->__('Gift Card Info')
        ));

        if ($card->getCardId()) {
            $fieldset->addField('card_id', 'hidden', array(
                'name' => 'card_id',
            ));
        }

        $fieldset->addField('card_code', 'label', array(
            'name'      => 'card_code',
            'label'     => $helper->__('Card Code'),
        ));
        
        $fieldset->addField('initial_value', 'text', array(
            'name'      => 'initial_value',
            'label'     => $helper->__('Card Value'),
            'value_filter' => $this,
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => $helper->__('Status'),
            'options'   => array(
                'A' => $helper->__('Active'),
                'I' => $helper->__('Inactive'),
            ),
        ));

        $fieldset->addField('gift_card_type', 'select', array(
            'name'      => 'gift_card_type',
            'label'     => $helper->__('Gift Card Type'),
            'options'   => array(
                'E' => $helper->__('E-mail'),
                'P' => $helper->__('Print'),
            ),
        ));

        $fieldset->addField('currency_code', 'hidden', array(
            'name'      => 'currency_code',
        ));

        $fieldset = $form->addFieldset('recipient_form', array(
            'legend'=>$helper->__('Recipient Info')
        ));
        
        $fieldset->addField('mail_address', 'text', array(
            'name'      => 'mail_address',
            'label'     => $helper->__('Recipient E-mail'),
        ));
        
        $fieldset->addField('mail_recipient', 'text', array(
            'name'      => 'mail_recipient',
            'label'     => $helper->__('To'),
        ));
        
        $fieldset->addField('mail_sender', 'text', array(
            'name'      => 'mail_sender',
            'label'     => $helper->__('From'),
        ));
        
        $fieldset->addField('mail_massege', 'textarea', array(
            'name'      => 'mail_massege',
            'label'     => $helper->__('Email message'),
            'style'     => 'height:70px',
        ));
        
        $fieldset->addField('mail_day2send', 'date', array(
            'name' => 'mail_day2send',
            'label' => $helper->__('Day to Send'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'time' => false,
        ));

        $this->setForm($form);
        $card->setData('mail_day2send', $card->getData('mail_day2send') == '0000-00-00' ? '' : $card->getData('mail_day2send'));
        $card->setData('currency_code', $card->getData('currency_code') == '' ? Mage::app()->getStore()->getDefaultCurrencyCode() : $card->getData('currency_code'));
        $form->setValues($card->getData());

        return parent::_prepareForm();
    }
    
    public function filter($value) {
    	return number_format($value, 2);
    }
}