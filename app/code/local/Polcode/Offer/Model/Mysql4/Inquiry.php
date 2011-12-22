<?php

class Polcode_Offer_Model_Mysql4_Inquiry extends Mage_Core_Model_Mysql4_Abstract {

    protected $_customerIdFieldName = 'customer_id';

    public function _construct() {
        $this->_init('offer/inquiry', 'inquiry_id');
    }

    public function getCustomerIdFieldName() {
        return $this->_customerIdFieldName;
    }

    public function loadInquiry($field, $value, $object)
    {
        $field  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($field . '=?', $value)
            ->where('`offer_inquiry`.`submitted`=0');
        
            $data = $this->_getReadAdapter()->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }        
        
        return $this;
    }   
    
    
}