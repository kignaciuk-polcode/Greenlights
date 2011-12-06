<?php
class Ebizmarts_Mailchimp_Model_Mysql4_Helper_Collection extends Varien_Data_Collection {

 	public function getSize(){

        $this->load();
        if (is_null($this->_totalRecords)) {
            $this->_totalRecords = count($this->getItems());
        }
        return intval($this->_totalRecords);
    }

	public function sortCollection($key, $dir) {

		$array = $this->getItems();

	    $values = array();
	    foreach ($array as $id => $value) {
	        $values[$id] = isset($value[$key]) ? strtolower($value[$key]) : '';
	    }

	    if (strtolower($dir)=='asc') {
	        asort($values);
	    }else {
	        arsort($values);
	    }

		$this->clear();

	    foreach ($values as $key => $value) {
	        $this->addItem($array[$key]);
	    }
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;
        $isPaginated = $this->getPageSize() > 0;

  		$cnt = 0;
        foreach ($this->getItems() as $k=>$row) {
            $cnt++;
            if ($isPaginated && ($cnt < $from || $cnt > $to)) {
            	$this->removeItemByKey($k);
            }
        }
	    return $this;
	}

  }
?>
