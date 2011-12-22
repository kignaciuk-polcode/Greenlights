<?php
class Webtex_Giftcards_Model_Validator extends Mage_SalesRule_Model_Validator
{
    public function processShippingAmount(Mage_Sales_Model_Quote_Address $address)
    {
        parent::processShippingAmount($address);
        if (Mage::getSingleton('giftcards/session')->getActive()=='1'){
        	$quote = $address->getQuote();
            $shippingAmount = $address->getShippingAmountForDiscount();
		    if ($shippingAmount!==null) {
		        $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
		    } else {
		        $shippingAmount     = $address->getShippingAmount();
		        $baseShippingAmount = $address->getBaseShippingAmount();
		    }

			$balance = $this->getAvailableGiftCardBalance() - $quote->getGiftcardAmount();
			if ($balance > 0) {
            	$discountAmount = min($shippingAmount - $address->getShippingDiscountAmount(), $quote->getStore()->convertPrice($balance));
            	$baseDiscountAmount = min($baseShippingAmount - $address->getBaseShippingDiscountAmount(), $balance);

            	$discountAmount     = min($discountAmount + $address->getShippingDiscountAmount(), $shippingAmount);
            	$baseDiscountAmount = min($baseDiscountAmount + $address->getBaseShippingDiscountAmount(), $baseShippingAmount);

            	$address->setShippingDiscountAmount($discountAmount);
            	$address->setBaseShippingDiscountAmount($baseDiscountAmount);
            	$quote->setGiftcardAmount($discountAmount+$quote->getGiftcardAmount());
			}
        }
    }

    /*
     * OMG!!!
     */
	public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
	{
		parent::process($item);
		if (Mage::getSingleton('giftcards/session')->getActive()=='1'){
			$address = $this->_getAddress($item);
			$cart_summary_count = $this->getFullItemNumber($address);
			if (!Mage::getSingleton('giftcards/session')->getProductChecked()) {
				Mage::getSingleton('giftcards/session')->setProductChecked('0');
				$item->getQuote()->setGiftcardAmount(0);
                $item->getQuote()->setGiftcardCode($this->getGiftCardSCodes());
			}
			Mage::getSingleton('giftcards/session')->setProductChecked(Mage::getSingleton('giftcards/session')->getProductChecked() + $item->getQty());

            // calculate row totals including tax
            $row_total = Mage::helper('tax')->getPrice(Mage::getModel('catalog/product')->load($item->getProductId()), $this->_getItemPrice($item) * $item->getTotalQty(), true, $address);
            $row_base_total = Mage::helper('tax')->getPrice(Mage::getModel('catalog/product')->load($item->getProductId()), $this->_getItemBasePrice($item) * $item->getTotalQty(), true, $address);
			$row_total = $item->getStore()->roundPrice($row_total);
			$row_base_total = $item->getStore()->roundPrice($row_base_total);

			$giftCardCurrentBalance = $this->getAvailableGiftCardBalance() - $item->getQuote()->getGiftcardAmount();
			$discountAmount = min($row_total - $item->getDiscountAmount(), $giftCardCurrentBalance);
			$baseDiscountAmount = min($row_base_total - $item->getBaseDiscountAmount(), $giftCardCurrentBalance);

			$item->getQuote()->setGiftcardAmount($discountAmount + $item->getQuote()->getGiftcardAmount());
			$item->getQuote()->setGiftcardCode($item->getQuote()->getGiftcardCode());

			$discountAmount     = min($discountAmount + $item->getDiscountAmount(), $row_total);
			$baseDiscountAmount = min($baseDiscountAmount + $item->getBaseDiscountAmount(), $row_base_total);

			$item->setDiscountAmount($discountAmount);
			$item->setBaseDiscountAmount($baseDiscountAmount);

			$couponCode = explode(', ', $address->getCouponCode());

			$descriptionPromo = $address->getDiscountDescriptionArray();
			if (sizeof($descriptionPromo)){
				$return_array = array();
				foreach($descriptionPromo as $val_desc){
					$return_array[] = $val_desc;
				}
				$descriptionPromo = $return_array;
			}
			if (sizeof($couponCode)){
				foreach($couponCode as $key_promo => $value_promo){
					if (isset($descriptionPromo[$key_promo])){
						$couponCode[$key_promo] = $descriptionPromo[$key_promo];
					}
				}
			}

			$couponCode[] = 'Gift Card';
			$couponCode = array_unique(array_filter($couponCode));
			if (version_compare(Mage::getVersion(), '1.4.0', '<')){
				$address->setCouponCode(implode(', ', $couponCode));
			}
			$address->setDiscountDescriptionArray($couponCode);

			if (Mage::getSingleton('giftcards/session')->getProductChecked() >= $cart_summary_count){
				Mage::getSingleton('giftcards/session')->setProductChecked(0);
			}
		} else {
			$item->getQuote()->setGiftcardAmount('');
			$item->getQuote()->setGiftcardCode('');
		}

		//return $this;
	}

	public function getFullItemNumber(Mage_Sales_Model_Quote_Address $address)
	{
		$items = $address->getAllItems();
		if (!count($items)) {
			return $this;
		}
		$i = 0;
		foreach ($items as $item) {
			if (!$item->getParentItemId()) {
				if ($item->getHasChildren() && $item->isChildrenCalculated()) {
					foreach ($item->getChildren() as $child) {
						$i = $i + $child->getQty();
					}
				} else {
					$i = $i + $item->getQty();
				}
			}
		}
		return $i;
	}

    public function getAvailableGiftCardBalance()
    {
        $cards = Mage::getResourceModel('giftcards/card_collection');
        $cards->getSelect()->where('main_table.customer_id = ?', Mage::getSingleton('customer/session')->getCustomerId());
        $balance = 0;
        foreach ($cards as $card) {
            $balance += $card->getCurrentBalance();
        }
        return $balance;
    }

    public function getGiftCardSCodes()
    {
        $cards = Mage::getResourceModel('giftcards/card_collection');
        $cards->getSelect()->where('main_table.customer_id = ?', Mage::getSingleton('customer/session')->getCustomerId());
        $codes = join(',', $cards->getColumnValues('card_code'));
        return $codes;
    }
}
