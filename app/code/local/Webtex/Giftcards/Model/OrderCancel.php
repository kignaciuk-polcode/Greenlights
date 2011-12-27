<?php
class Webtex_Giftcards_Model_OrderCancel {
	public function __construct(){}
	
	public function cancelOrder($observer){
		$order = $observer->getEvent()->getOrder();
		$cardData = Mage::getResourceModel('giftcards/card_collection');
		$cardData->getSelect()->where('main_table.card_code IN (?)', explode(",", $order->getGiftcardCode()));
		$cardData->load();
		$balance_total = $order->getGiftcardAmount();
		foreach ($cardData->getItems() as $value) {
			if ($balance_total == 0) break;
			$data = $balance_total - ($value->getInitialValue()-$value->getCurrentBalance());
			if ($data >= 0) {
				$data = $value->getInitialValue()-$value->getCurrentBalance();
				$balance_total = $balance_total - $data;
			} else {
				$data = $balance_total;
				$balance_total = 0;
			}
			$value->setCurrentBalance($data+$value->getCurrentBalance());
			$value->save();
		}
		return $this;
	}
}