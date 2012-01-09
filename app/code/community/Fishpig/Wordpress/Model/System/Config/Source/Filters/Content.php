<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Filters_Content
{
	public function toOptionArray()
	{
		$filters = array('' => 'Disable All', 'shortcodes' => 'Render Shortcodes', 'auto_paragraph' => 'Auto Add P Tags');
		$options = array();
		
		foreach($filters as $value => $label) {
			$options[] = array('value' => $value, 'label' => $label);
		}

		return $options;
	}
}
