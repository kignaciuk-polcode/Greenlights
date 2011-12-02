<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Filters_Excerpt
{
	public function toOptionArray()
	{
		$filters = array('' => 'Disable All', 'remove_html' => 'Remove HTML', 'shortcodes' => 'Render Shortcodes', 'remove_shortcodes' => 'Remove Shortcodes');
		$options = array();
		
		foreach($filters as $value => $label) {
			$options[] = array('value' => $value, 'label' => $label);
		}

		return $options;
	}
}
