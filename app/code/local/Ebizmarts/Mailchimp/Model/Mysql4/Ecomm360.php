<?php

class Ebizmarts_Mailchimp_Model_Mysql4_Ecomm360 extends Mage_Core_Model_Mysql4_Abstract{

	public function _construct(){

		$this->_init('mailchimp/ecomm360', 'ecomm_id');
	}
}