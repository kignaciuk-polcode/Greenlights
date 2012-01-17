<?php

class Devinc_Multipledeals_Block_Adminhtml_Widget_Grid_Column_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{

    /**
     * Renders column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
    	$actions = $this->getColumn()->getActions();
		if ( empty($actions) || !is_array($actions) ) {
		    return '&nbsp';
		}

		if(sizeof($actions)==1 && !$this->getColumn()->getNoLink()) {
		    foreach ($actions as $action){
                if ( is_array($action) ) {
                    return $this->_toLinkHtml($action, $row);
            	}
            }
		}

		$out = '<select class="action-select" onchange="varienGridAction.execute(this);">'
		     . '<option value=""></option>';
		$i = 0;
        foreach ($actions as $action){
            $i++;
        	if ( is_array($action) ) {
        		$display = true;
				if (array_key_exists('condition', $action)) {
					$conditions = $action['condition'];
					foreach ($conditions as $condition) {
						if (is_array($condition)) {
							$display = $display && $this->_checkCondition($row, $condition);
						} else {
							$display = $this->_checkCondition($row, $conditions);
							break;
						}
					}
				}

				if ($display) {
	                $out .= $this->_toOptionHtml($action, $row);
				}
        	}
        }
		$out .= '</select>';
		return $out;
    }

    /**
     * Check condition
     *
     * @param Varien_Object $row
     * @param Array $condition
     * @return boolean
     */
	protected function _checkCondition($row, $condition) {
		if (!array_key_exists('data', $condition) || !array_key_exists('operator', $condition) || !array_key_exists('value', $condition)) {
			return true;
		} else {
			$op = $condition['operator'];
			if ($op == 'eq' || $op == '==') {
				return ($row->getData($condition['data']) == $condition['value']);
            } elseif ($op == 'neq' || $op == '!=') {
				return ($row->getData($condition['data']) != $condition['value']);
			} elseif ($op == 'gt' || $op == '>') {
				return ($row->getData($condition['data']) > $condition['value']);
            } elseif ($op == 'lt' || $op == '<') {
				return ($row->getData($condition['data']) < $condition['value']);
            } elseif ($op == 'gteq' || $op == '>=') {
				return ($row->getData($condition['data']) >= $condition['value']);
            } elseif ($op == 'lteq' || $op == '<=') {
				return ($row->getData($condition['data']) <= $condition['value']);
			}
		}

		return true;
	}

}