<?php
class Ebizmarts_Mailchimp_Model_Mysql4_Subscripter_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

      public function _construct(){

          $this->_init('mailchimp/subscripter');
      }

/*******************************
 *
 * hack for old Magentos
 *
 ********************************/
	public function addFieldToSelect($field, $alias = null){
        if ($field === '*') { // If we will select all fields
            $this->_fieldsToSelect = null;
            $this->_fieldsToSelectChanged = true;
            return $this;
        }

        if (is_array($field)) {
            if ($this->_fieldsToSelect === null) {
                $this->_fieldsToSelect = $this->_getInitialFieldsToSelect();
            }

            foreach ($field as $key => $value) {
                $this->addFieldToSelect(
                    $value,
                    (is_string($key) ? $key : null),
                    false
                );
            }

            $this->_fieldsToSelectChanged = true;
            return $this;
        }

        if ($alias === null) {
            $this->_fieldsToSelect[] = $field;
        } else {
            $this->_fieldsToSelect[$alias] = $field;
        }

        $this->_fieldsToSelectChanged = true;
        return $this;
    }

  }
?>