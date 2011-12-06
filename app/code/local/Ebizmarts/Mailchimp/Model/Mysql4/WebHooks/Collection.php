<?php
class Ebizmarts_Mailchimp_Model_Mysql4_WebHooks_Collection extends Varien_Data_Collection {

 	public function getSize(){

        $this->load();
        if (is_null($this->_totalRecords)) {
            $this->_totalRecords = count($this->getItems());
        }
        return intval($this->_totalRecords);
    }

  }
?>
