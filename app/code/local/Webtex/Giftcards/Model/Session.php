<?php

class Webtex_Giftcards_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $namespace = 'giftcards';
        $this->init($namespace);
    }

    public function unsetAll()
    {
        parent::unsetAll();
    }
}
