<?php
class Webtex_Giftcards_Model_Card extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/card');
        parent::_construct();
    }

    public function activateCardForCustomer($customerId)
    {
        if ($this->getId()) {
            $this->setStatus('I');
            $this->setCustomerId($customerId);
            $this->save();
        }
    }

    public function getUniqueCardCode()
    {
        $cardCodes = $this->getResourceCollection()->getColumnValues('card_code');

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $codeUnique = true;
            $cardCode = '';
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 4; $j++) {
                    $cardCode .= $characters[mt_rand(0, strlen($characters)-1)];
                }
                if ($i != 2) {
                    $cardCode .= '-';
                }
            }

            if (in_array($cardCode, $cardCodes)) {
                $codeUnique = false;
            }
        } while (!$codeUnique);
        
        return $cardCode;
    }

    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setDateCreated(now());
            $this->setCardCode($this->getUniqueCardCode());
            $this->setCurrentBalance($this->getInitialValue());
        }
    }
}
