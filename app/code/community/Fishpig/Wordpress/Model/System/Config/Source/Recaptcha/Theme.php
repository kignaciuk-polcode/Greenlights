<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Recaptcha_Theme
{
	public function toOptionArray()
	{
		$themes = array('red' => 'Red', 'white' => 'White', 'blackglass' => 'Black Glass', 'clean' => 'Clean', 'custom' => 'Custom');
		$options = array();
		
		foreach($themes as $value => $label) {
			$options[] = array('value' => $value, 'label' => $label);
		}
		
		return $options;
	}
}
