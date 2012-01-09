<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Shortcodes
{
	/**
	 * Retrieve an array of shortcodes
	 *
	 * @return array
	 */
	public function toOptionArray()
	{ 
		$shortcodes = $this->getOptions();
		$options = array();
		
		foreach($shortcodes as $value => $label) {
			$options[] = array('value' => $value, 'label' => $label);
		}

		return $options;
	}
	
	/**
	 * Retrieve all supported shortcodes
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return array(
			'' => 'Disable All', 
			'caption' => 'Caption', 
			'youtube' => 'YouTube Videos', 
			'product' => 'Featured Product (Custom)', 
			'associated_product' => 'Associated Products (Custom)'
		);
	}
}
