<?php
class Webtex_Giftcards_Model_Cron
{
	public function sendMail() {
		$cardData = Mage::getResourceModel('giftcards/card_collection');
        $cardData->getSelect()->join(
                array('order' => $cardData->getTable('sales/order')),
                'main_table.order_id=order.entity_id',
                array('customer_email', 'store_id')
            )
            ->where('order.status in (?)', array('complete'))
            ->where('main_table.mail_day2send <= ?', date('Y-m-d'))
            ->where('not main_table.is_mail_sent');
		foreach ($cardData->getItems() as $item) {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            $mailTemplate = Mage::getModel('core/email_template');

            if ($item->getGiftCardType() == 'P'){
                $post = array(
                    'amount'        => Mage::helper('core')->currency($item->getInitialValue(), true, false),
                    'email-to'      => $item->getMailRecipient(),
                    'email-from'    => $item->getMailSender(),
                    'code'          => $item->getCardCode(),
                    'link'          => Mage::getUrl('giftcards/customer/printgiftcard/') . 'id/' . $item->getCardId(),
                    'email-message' => $item->getMailMassege(),
                    'store-phone'   => Mage::getStoreConfig('general/store_information/phone'),
                );

                $postObject = new Varien_Object();
                $postObject->setData($post);
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $item->getStoreId()))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig('giftcards/email/print_template'),
                        'general',
                        $item->getCustomerEmail(),
                        null,
                        array('data' => $postObject)
                    );

            } elseif ($item->getGiftCardType() == 'E') {
                $post = array(
                    'amount'        => Mage::helper('core')->currency($item->getInitialValue(), true, false),
                    'email-to'      => $item->getMailRecipient(),
                    'email-from'    => $item->getMailSender(),
                    'recipient'     => $item->getMailAddress(),
                    'code'          => $item->getCardCode(),
                    'email-message' => $item->getMailMassege(),
                    'store-phone'   => Mage::getStoreConfig('general/store_information/phone'),
                );
    
                $postObject = new Varien_Object();
                $postObject->setData($post);
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $item->getStoreId()))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig('giftcards/email/email_template'),
                        'general',
                        $item->getMailAddress(),
                        null,
                        array('data' => $postObject)
                    );
            }

            $translate->setTranslateInline(true);
            if ($mailTemplate->getSentSuccess()) {
                $item->setIsMailSent(1)->save();

                // send confirmation
                $mailTemplate = Mage::getModel('core/email_template');
                $post = array(
                    'store-phone'   => Mage::getStoreConfig('general/store_information/phone'),
                );
                $postObject = new Varien_Object();
                $postObject->setData($post);
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $item->getStoreId()))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig('giftcards/email/confirm_template'),
                        'general',
                        $item->getCustomerEmail(),
                        null,
                        array('data' => $postObject)
                    );
            }
		}
		return $this;
	}
}