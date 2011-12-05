<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Sql_Operator
{
	public function toOptionArray()
	{
		$operators = array('or', 'and');
		$options = array();
		
		foreach($operators as $operator) {
			$options[] = array(
				'value' => $operator, 
				'label' => strtoupper($operator)
			);
		}
		
		return $options;
	}
}
