<?php

class Ebizmarts_Mailchimp_Model_Mysql4_Subscripter extends Mage_Core_Model_Mysql4_Abstract{

	public function _construct(){

        $this->_init('mailchimp/subscripter', 'mailchimppro_id');
	}
}