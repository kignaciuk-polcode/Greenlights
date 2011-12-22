<?php

class Polcode_Offer_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->_redirect('*/inquiry/history');
    }

}
