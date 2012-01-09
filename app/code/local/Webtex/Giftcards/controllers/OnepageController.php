<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Webtex_Giftcards_OnepageController extends Mage_Checkout_OnepageController
{
    /**
     * Create order action
     */
    public function saveOrderAction()
    {
		$this->getOnepage()->getQuote()->collectTotals()->save();
    	parent::saveOrderAction();

    	try {
            foreach ($this->getOnepage()->getQuote()->getAllVisibleItems() as $item) {
                if ($item->getProduct()->getTypeId() == 'giftcards'){
                    $options = $item->getProduct()->getCustomOptions();
                    if (isset($options['option_0'])) { $price = $options['option_0']->getValue(); }
                    if (isset($options['option_1'])) { $options['option_1']->setValue($options['option_1']->getValue() == 'E' ? 'E' : 'P'); }
                    $optionsDataMap = array(
                        'option_1' => 'gift_card_type',
                        'option_2' => 'mail_recipient',
                        'option_3' => 'mail_sender',
                        'option_4' => 'mail_massege',
                        'option_5' => 'mail_day2send',
                        'option_6' => 'mail_address',
                    );
                    $data = array();
                    foreach ($optionsDataMap as $from => $to) {
                        if (isset($options[$from])) {
                            $data[$to] = $options[$from]->getValue();
                        }
                    }

                    $order = Mage::getModel('sales/order')->load($this->getOnepage()->getCheckout()->getLastOrderId());
                    
                    for ($i=0; $i<$item->getQty(); $i++) {
                        $model = Mage::getModel('giftcards/card');
                        $model->setData($data)
                            ->setInitialValue($price)
                            ->setStatus('A')
                            ->setOrderId($this->getOnepage()->getCheckout()->getLastOrderId())
                            ->setCurrencyCode(Mage::app()->getStore()->getDefaultCurrencyCode());

                        if ($model->getGiftCardType() == 'P'){
                            $model->setMailAddress($order->getCustomerEmail());
                        }
                        $model->save();
                    }

                }
            }
            if (strlen($this->getOnepage()->getQuote()->getGiftcardCode())) {
                $cards = Mage::getResourceModel('giftcards/card_collection');
                $cards->getSelect()->where('main_table.card_code IN (?)', explode(",", $this->getOnepage()->getQuote()->getGiftcardCode()));
                $value = $this->getOnepage()->getQuote()->getGiftcardAmount();
                foreach ($cards as $card) {
                    $useAmount = min($card->getCurrentBalance(), $value);
                    if ($useAmount > 0) {
                        $value -= $card->getCurrentBalance();
                        $this->getOnepage()->getQuote()->setGiftcardAmount($value);
                        $card->setCurrentBalance($card->getCurrentBalance() - $useAmount);
                        $card->save();
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
    }
}