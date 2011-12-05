<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Post_Fields
{
	public function toOptionArray()
	{
		$fields = array('post_title', 'post_content', 'post_excerpt', 'post_name');
		$options = array();
		
		foreach($fields as $field) {
			$options[] = array(
				'value' => $field, 
				'label' => ucwords(str_replace('_', ' ', $field))
			);
		}
		
		return $options;
	}
}
